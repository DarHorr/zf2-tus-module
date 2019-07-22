<?php
namespace Zf2Tus\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zf2Tus\Exception\InvalidConfigurationException;
use ZfTusServer\Server;

class IndexController extends AbstractActionController {

    /**
     * @var array
     */
    private $config;

    public function __construct(array $config) {

        if (!isset($config['uploading']['zf_tus_server'])) {
            throw new InvalidConfigurationException('Error in configuration of ZfTusServer - no [uploading][zf_tus_server] keys in configuration.');
        }
        $this->config = $config['uploading']['zf_tus_server'];
    }

    public function indexAction() {

        if (isset($this->config['storage_patch'])) {
            $storeLocation = $this->config['storage_patch'];
        }
        else {
            throw new InvalidConfigurationException('Error in configuration of ZfTusServer storage path.');
        }

        if (!file_exists($storeLocation)) {
            if (!mkdir($storeLocation, $this->config['dirChmod']) && !is_dir($storeLocation)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $storeLocation));
            }
        }

        $debug = $this->config['allow_download_info'] ?? false;

        // Create and configure server
        $server = new Server(
                $storeLocation, $this->getRequest(), $debug
        );

        // Run server
        $server->process(true);
    }
}
