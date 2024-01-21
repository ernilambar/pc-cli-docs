<?php
/**
 * Command
 *
 * @package pc-cli-docs
 */

$command = new PCCLIDocs();

WP_CLI::add_command( 'pc-cli-docs', $command );
