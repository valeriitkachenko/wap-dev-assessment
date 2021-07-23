<?php


namespace App\Services\Marketplace;

use App\Models\Order;
use App\Models\Product;
use App\Services\Marketplace\Exceptions\InvalidConfigException;
use App\Services\Marketplace\Exceptions\ResponseParsingException;
use App\Services\Marketplace\Helpers\DataParser;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Model;

class MarketplaceService
{
    const TYPE_PRODUCT = 'product';
    const TYPE_ORDER = 'order';

    const AVAILABLE_TYPES = [
        self::TYPE_PRODUCT,
        self::TYPE_ORDER,
    ];

    /**
     * @var MarketplaceClient $client
     */
    private $client;

    /**
     * MarketplaceService constructor.
     *
     * @param array $config
     * @throws InvalidConfigException
     */
    public function __construct(array $config)
    {
        $this->client = new MarketplaceClient($config);
    }

    /**
     * @return Model
     * @throws ResponseParsingException
     * @throws GuzzleException
     */
    public function getProductOrOrderEntity(): Model
    {
        $productOrOrder = self::getProductOrOrder();
        $type = $productOrOrder['type'];
        $properties = $productOrOrder['properties'];

        if (!in_array($type, self::AVAILABLE_TYPES)) {
            throw new Exception('Couldn\'t create an entity');
        }

        if ($type == self::TYPE_ORDER) {
            $properties['external_id'] = $properties['id'];

            $model = Order::firstOrNew([
                'external_id' => $properties['external_id']
            ], $properties);
        } else {
            $model = Product::firstOrNew([
                'SKU' => $properties['SKU']
            ], $properties);
        }

        return $model;
    }

    /**
     * @return array
     * @throws ResponseParsingException
     * @throws GuzzleException
     */
    public function getProductOrOrder(): array
    {
        $serializedEntry = $this->client->getProductOrOrder();

        return $this->unserialize($serializedEntry);
    }

    /**
     * @param string $entry
     * @return array
     * @throws ResponseParsingException
     */
    public function unserialize(string $entry): array
    {
        return DataParser::parse($entry);
    }
}
