<?php 
namespace App\Service;
use Psr\Log\LoggerInterface;

class RandomService {
  private $counter = 0;
  private $logger;
  public function __construct(LoggerInterface $logger) {
    $this->logger = $logger;
    print $this->logger->info("random service constructed")."\n";
  }
  public function getCounter() {

    $this->counter += 1;
    return $this->counter;
  }
  public function random() {
    return random_int(0, 100);
  }
}