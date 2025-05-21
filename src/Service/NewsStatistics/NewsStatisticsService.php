<?php
namespace App\Service\NewsStatistics;

use App\Config\EmailConfig;
use App\DTO\TopNewsStatisticsDTO;
use App\Repository\NewsRepository;
use App\Service\Email\NewsEmailService;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Clock\ClockInterface;
use Psr\Cache\CacheItemPoolInterface;

class NewsStatisticsService implements NewsStatisticsServiceInterface
{
    private const CACHE_KEY = 'top_news_statistics';
    private const CACHE_TTL = 86400; // 24 hours in seconds

    public function __construct(
        private readonly NewsRepository $newsRepository,
        private readonly ClockInterface $clock,
        private readonly EmailConfig $emailConfig,
        private readonly NewsEmailService $emailService,
        private readonly CacheItemPoolInterface $cache
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function generateTopNewsStatisticsDTO(): TopNewsStatisticsDTO
    {
        $cacheItem = $this->cache->getItem(self::CACHE_KEY);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $periodEnd = $this->clock->now();
        $periodStart = $periodEnd->modify('-7 days');

        $topNews = $this->newsRepository->findTopViewedNews(10, $periodStart, $periodEnd);

        $newsItems = array_map(
            fn($news) => [
                'id' => $news->getId(),
                'title' => $news->getTitle(),
                'views' => $news->getViews()
            ],
            $topNews
        );

        $statistics = new TopNewsStatisticsDTO(
            $newsItems,
            $periodStart,
            $periodEnd,
            $this->emailConfig->getAdminEmail()
        );

        $cacheItem->set($statistics);
        $cacheItem->expiresAfter(self::CACHE_TTL);
        $this->cache->save($cacheItem);

        return $statistics;
    }

    public function sendTopNewsStatistics(?TopNewsStatisticsDTO $dto = null): bool
    {
        try {
            $dto = $dto ?? $this->generateTopNewsStatisticsDTO();
            return $this->emailService->sendNewsStatisticsEmail($dto);
        } catch (Exception|InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function reset(): void
    {
        $this->cache->deleteItem(self::CACHE_KEY);
    }
}
