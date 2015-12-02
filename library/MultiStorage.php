<?php
namespace Imbo\MultiBackend;

use Imbo\Exception\StorageException,
    Exception;

/**
 * Multiple storage adapter
 *
 * @author Espen Hovlandsdal <espen@hovlandsdal.com>
 */
class MultiStorage implements StorageInterface {
    /**
     * Holds an array of backends that should be used
     *
     * @var array
     */
    protected $backends;

    /**
     * Construct a new multi storage adapter
     *
     * @param array $params
     */
    public function __construct(array $params) {
        if (!isset($params['backends']) || !is_array($params['backends'])) {
            throw new InvalidArgumentException(
                '`backends` must be specified and must be an array'
            );
        }

        foreach ($params['backends'] as $backend) {
            if ($backend instanceof StorageInterface) {
                throw new InvalidArgumentException(
                    'All backends must implement `Imbo\Storage\StorageInterface`'
                );
            }
        }

        if (empty($params['backends'])) {
            throw new InvalidArgumentException('Must specify at least one storage backend');
        }

        $this->backends = $params['backends'];
    }

    /**
     * {@inheritdoc}
     */
    public function store($user, $imageIdentifier, $imageData) {
        $success = true;
        foreach ($this->backends as $backend) {
            $success = $backend->store($user, $imageIdentifier, $imageData) && $success;
        }
        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($user, $imageIdentifier) {
        $success = true;
        foreach ($this->backends as $backend) {
            try {
                $success = $backend->delete($user, $imageIdentifier) && $success;
            } catch (Exception $e) {
                if ($e->getCode() !== 404) {
                    throw $e;
                }
            }
        }
        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function getImage($user, $imageIdentifier) {
        foreach ($this->backends as $backend) {
            try {
                return $backend->getImage($user, $imageIdentifier);
            } catch (Exception $e) {
                if ($e->getCode() !== 404) {
                    throw $e;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLastModified($user, $imageIdentifier) {
        foreach ($this->backends as $backend) {
            try {
                return $backend->getLastModified($user, $imageIdentifier);
            } catch (Exception $e) {
                if ($e->getCode() !== 404) {
                    throw $e;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus() {
        $success = true;
        foreach ($this->backends as $backend) {
            $success = $backend->getStatus() && $success;
        }
        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function imageExists($user, $imageIdentifier) {
        foreach ($this->backends as $backend) {
            if ($backend->imageExists($user, $imageIdentifier)) {
                return true;
            }
        }
        return false;
    }
}
