<?php

namespace App\Components\Backup\Config;

use App\Components\Backup\Concerns\UsesBackupConfigurationProvider;
use Spatie\Backup\Config\DestinationConfig;

class DatabaseDestinationConfigProvider extends DestinationConfig
{
	use UsesBackupConfigurationProvider;

	public function __construct(DestinationConfig $original)
	{
		parent::__construct(
			compressionMethod: $original->compressionMethod,
			compressionLevel: $original->compressionLevel,
			filenamePrefix: $original->filenamePrefix,
			disks: $this->getDisks(),
		);
	}

	/**
	 * Gets disks to use for backups
	 *
	 * @return array
	 */
	public function getDisks(): array
	{
		return $this->getConfigurationProvider()->getDisks();
	}
}
