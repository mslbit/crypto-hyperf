<?php

declare(strict_types=1);

namespace Maiscraft\CryptoHyperf;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Maiscraft\Crypto\AesCbcEncrypter;
use Maiscraft\Crypto\OpenSSLEncrypter;
use Maiscraft\Crypto\RsaEncrypter;
use Maiscraft\Crypto\SodiumEncrypter;

/**
 * 生成加密密钥命令
 *
 * 用法：
 *   php bin/hyperf.php crypto:generate-key              # 生成所有对称密钥
 *   php bin/hyperf.php crypto:generate-key --driver=rsa  # 生成 RSA 密钥对
 *   php bin/hyperf.php crypto:generate-key --driver=openssl --bits=256
 */
#[Command]
class GenerateKeyCommand extends HyperfCommand
{
    protected ?string $name = 'crypto:generate-key';

    public function configure()
    {
        $this->setDescription('Generate encryption keys for crypto module')
            ->addOption('driver', 'd', 4, 'Driver: openssl, aes-cbc, sodium, rsa', 'all')
            ->addOption('bits', 'b', 4, 'Key size in bits (RSA: 2048/4096, AES: 128/256)', 256);
    }

    public function handle()
    {
        $driver = $this->input->getOption('driver');
        $bits = (int) $this->input->getOption('bits');

        match ($driver) {
            'all' => $this->generateAll($bits),
            'openssl' => $this->generateOpenSSL($bits),
            'aes-cbc' => $this->generateAesCbc($bits),
            'sodium' => $this->generateSodium(),
            'rsa' => $this->generateRsa($bits),
            default => $this->output->error("Unknown driver: {$driver}. Supported: openssl, aes-cbc, sodium, rsa, all"),
        };
    }

    protected function generateAll(int $bits): void
    {
        $this->output->section('Generating all symmetric keys');
        $this->generateOpenSSL($bits);
        $this->generateAesCbc($bits);
        $this->generateSodium();
        $this->output->section('Generating RSA key pair');
        $this->generateRsa(2048);
    }

    protected function generateOpenSSL(int $bits): void
    {
        $cipher = $bits === 128 ? 'aes-128-gcm' : 'aes-256-gcm';
        $key = OpenSSLEncrypter::generateKey($cipher);

        $this->output->writeln("<info>OpenSSL ({$cipher}):</info>");
        $this->output->writeln("  CRYPTO_OPENSSL_KEY={$key}");
        $this->output->newLine();
    }

    protected function generateAesCbc(int $bits): void
    {
        $cipher = $bits === 128 ? 'aes-128-cbc' : 'aes-256-cbc';
        $key = AesCbcEncrypter::generateKey($cipher);

        $this->output->writeln("<info>AES-CBC ({$cipher}):</info>");
        $this->output->writeln("  CRYPTO_AES_CBC_KEY={$key}");
        $this->output->newLine();
    }

    protected function generateSodium(): void
    {
        $key = SodiumEncrypter::generateKey();

        $this->output->writeln('<info>Sodium (XChaCha20-Poly1305):</info>');
        $this->output->writeln("  CRYPTO_SODIUM_KEY={$key}");
        $this->output->newLine();
    }

    protected function generateRsa(int $bits): void
    {
        $pair = RsaEncrypter::generateKeyPair($bits);

        $this->output->writeln("<info>RSA ({$bits} bits):</info>");
        $this->output->writeln('  CRYPTO_RSA_PRIVATE_KEY=');
        $this->output->writeln('    <comment>' . str_replace("\n", "\n    ", $pair['private_key']) . '</comment>');
        $this->output->writeln('  CRYPTO_RSA_PUBLIC_KEY=');
        $this->output->writeln('    <comment>' . str_replace("\n", "\n    ", $pair['public_key']) . '</comment>');
        $this->output->newLine();

        $this->output->writeln('<comment>Tip: Store RSA keys in files and use file:// path in config:</comment>');
        $this->output->writeln('  CRYPTO_RSA_PRIVATE_KEY=file:///path/to/private.pem');
        $this->output->writeln('  CRYPTO_RSA_PUBLIC_KEY=file:///path/to/public.pem');
    }
}