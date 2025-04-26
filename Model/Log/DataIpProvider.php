<?php

namespace Orangecat\Checkip\Model\Log;

use DateTime;
use Magento\Framework\Api\Filter;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Orangecat\Checkip\Model\Service\LogIpService;

class DataIpProvider extends AbstractDataProvider
{
    /** @var File */
    protected $fileDriver;

    /** @var RequestInterface */
    protected $request;

    /** @var File */
    protected $sortField;

    /** @var $sortDirection */
    protected $sortDirection;

    /** @var $logFile */
    protected $logFile;

    /** @var $loadedData */
    protected $loadedData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param File $fileDriver
     * @param RequestInterface $request
     * @param EntityFactoryInterface $entityFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        File $fileDriver,
        RequestInterface $request,
        EntityFactoryInterface $entityFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->fileDriver = $fileDriver;
        $this->request = $request;
        $collection = new Collection($entityFactory);
        $this->collection = $collection;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Add Order
     *
     * @param string $field
     * @param string $direction
     */
    public function addOrder($field, $direction)
    {
        $this->sortField = $field;
        $this->sortDirection = strtolower($direction) === 'asc' ? SORT_ASC : SORT_DESC;
    }

    /**
     * Add Filter
     */
    public function addFilter(Filter $filter)
    {
    }

    /**
     * Get Data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $moment = 'Today';
        $this->logFile = BP . '/var/ipblacklist/log/' . LogIpService::LOG_IP_FILENAME . '.log';
        $filters = $this->request->getParam('filters');
        if (isset($filters['logfile'])) {
            $this->logFile = BP . '/var/ipblacklist/log/' . $filters['logfile'];
            $moment = 'Past';
            if (preg_match('/\d{4}-\d{2}-\d{2}/', $filters['logfile'], $date)) {
                $moment = $date[0];
            }
        }

        $items = [];
        $i = 0;

        if ($this->fileDriver->isExists($this->logFile)) {
            $lines = $this->fileDriver->fileGetContents($this->logFile);
            $lines = explode("\n", $lines);

            // Filtros

            if (isset($filters['date']['from'])) {
                $datefrom = DateTime::createFromFormat('n/j/Y', $filters['date']['from']);
            } else {
                $datefrom = null;
            }

            if (isset($filters['date']['to'])) {
                $dateto = DateTime::createFromFormat('n/j/Y', $filters['date']['to']);
            } else {
                $dateto = null;
            }

            $filterUserAgent = $filters['user_agent'] ?? null;
            $filterIp = $filters['ip'] ?? null;

            $this->collection->clear();

            foreach ($lines as $line) {
                if (empty($line)) {
                    continue;
                }

                [$date, $time, $ip, $cidr, $userAgent] = explode('|', $line);
                $userAgent = trim($userAgent);

                if ($filterIp && stripos(trim($ip), trim($filterIp, '%')) === false) {
                    continue;
                }

                // Filtro por fecha
                $datecollection = DateTime::createFromFormat('Y-m-d', $date);
                if ($datefrom !== null && $dateto !== null) {
                    if ($datecollection < $datefrom || $datecollection > $dateto) {
                        continue;
                    }
                }

                // Filtro por User-Agent
                if ($filterUserAgent && stripos($userAgent, trim($filterUserAgent, '%')) === false) {
                    continue;
                }
                $i++;

                $items[] = [
                    'id' => $i,
                    'date' => $date,
                    'time' => $time,
                    'ip' => trim($ip),
                    'user_agent' => $userAgent,
                    'logfile' => $moment,
                ];
            }
        }

        $this->collection->clear();
        foreach ($items as $item) {
            $this->collection->addItem(new DataObject($item));
        }

        if (!empty($items) && isset($this->sortField)) {
            $sortField = $this->sortField;
            $direction = $this->sortDirection;

            usort($items, function ($a, $b) use ($sortField, $direction) {
                return $direction === SORT_ASC
                    ? ($a[$sortField] <=> $b[$sortField])
                    : ($b[$sortField] <=> $a[$sortField]);
            });
        }

        $paging = $this->request->getParam('paging');
        $pageSize = isset($paging['pageSize']) ? (int)$paging['pageSize'] : 20;
        $currentPage = isset($paging['current']) ? (int)$paging['current'] : 1;
        $offset = ($currentPage - 1) * $pageSize;

        $paginatedItems = array_slice($items, $offset, $pageSize);

        $this->loadedData = [
            'totalRecords' => count($items),
            'items' => $paginatedItems
        ];

        return $this->loadedData;
    }
}
