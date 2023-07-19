<?php

namespace App\Service;

use App\Dto\ItemDto;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Item;
use Exception;
use Psr\Log\LoggerInterface;

class DictionaryService
{
    /**
     * @var ItemRepository
     */
    private $entityRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityRepository = $entityManager->getRepository(Item::class);
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function fill($content)
    {
        $xmlItems = simplexml_load_string($content);

        try {
            $this->entityManager->getConnection()->beginTransaction();

            $this->fillItemsGroup($xmlItems->xpath("items"));

            $this->entityManager->getConnection()->commit();
        } catch (Exception $exception) {
            $this->entityManager->getConnection()->rollBack();

            $this->writeErrorLog($exception);
        }
    }

    public function add(ItemDto $itemDto)
    {
        try {
            $parent = $this->entityRepository->findByCode($itemDto->code);

            $this->entityRepository->append(new Item($itemDto->item['code'], $itemDto->item['name']), $parent);
        } catch (Exception $exception) {
            $this->writeErrorLog($exception);
        }
    }

    public function edit(ItemDto $itemDto)
    {
        try {
            $item = $this->entityRepository->findByCode($itemDto->code);

            $item->code = $itemDto->item['code'];
            $item->name = $itemDto->item['name'];
            $this->entityManager->flush();
        } catch (Exception $exception) {
            $this->writeErrorLog($exception);
        }
    }

    public function delete(string $code)
    {
        try {
            $item = $this->entityRepository->findByCode($code);

            $this->entityManager->remove($item);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            $this->writeErrorLog($exception);
        }
    }

    public function findAll()
    {
        $allItems = $this->entityRepository->findAll();

        $preparedItems = [];

        foreach ($allItems as $item) {
            $preparedItems[$item->getRootKey()][$item->getLevel()] = $item;
        }

        return $preparedItems;
    }

    public function findByCodePart(string $code)
    {
        return $this->entityRepository->findByCodePart($code);
    }

    public function findByNamePart(string $name)
    {
        return $this->entityRepository->findByNamePart($name);
    }

    private function writeErrorLog(Exception $exception)
    {
        $this->logger->error($exception->getCode() . ',' . $exception->getMessage());
    }

    private function fillItemsGroup(array $items, $parent = null)
    {
        if ($parent) {
            $parent = $this->entityRepository->findByCode($parent->code);
        }

        foreach ($items as $item) {
            $this->entityRepository->append(new Item($item->code, $item->name), $parent);

            $children = $item->xpath("items");
            if ($children) {
                $this->fillItemsGroup($children, $item);
            }
        }
    }
}