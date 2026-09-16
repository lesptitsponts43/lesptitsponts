<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:sumup:register-webhook',
    description: 'Registers the SumUp webhook for payment notifications',
)]
class RegisterSumupWebhookCommand extends Command
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $sumupApiKey,
        private string $webhookUrl,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->sumupApiKey || $this->sumupApiKey === 'your_sumup_api_key_here') {
            $io->error('SUMUP_API_KEY is not configured. Please set it in your .env file.');
            return Command::FAILURE;
        }

        if (!$this->webhookUrl) {
            $io->error('WEBHOOK_URL is not configured. Please set it in your .env file.');
            return Command::FAILURE;
        }

        $io->writeln('Registering SumUp webhook...');
        $io->writeln('URL: ' . $this->webhookUrl);

        try {
            $response = $this->httpClient->request('POST', 'https://api.sumup.com/v0.1/me/webhooks', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->sumupApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'url' => $this->webhookUrl,
                    'events' => [
                        'TRANSACTION.SUCCESSFUL',
                        'TRANSACTION.FAILED',
                        'TRANSACTION.CANCELLED',
                    ],
                ],
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode === 201 || $statusCode === 200) {
                $data = $response->toArray();
                $io->success('Webhook registered successfully!');
                $io->writeln('Webhook ID: ' . ($data['id'] ?? 'N/A'));
                $io->writeln('URL: ' . ($data['url'] ?? 'N/A'));

                if (isset($data['secret'])) {
                    $io->warning('⚠️  IMPORTANT: Copy this secret and set it as SUMUP_WEBHOOK_SECRET in your .env:');
                    $io->writeln('<fg=yellow>' . $data['secret'] . '</>');
                }

                return Command::SUCCESS;
            } else {
                $io->error('Failed to register webhook. Status: ' . $statusCode);
                $io->writeln('Response: ' . $response->getContent());
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $io->error('Error registering webhook: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
