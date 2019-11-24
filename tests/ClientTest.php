<?php
declare(strict_types=1);

use Gentor\Olx\Api\Client;
use Gentor\Olx\Api\Credentials;
use Gentor\Olx\Api\OlxException;
use Gentor\Olx\Utils\AdvertBuilder;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    const CONFIG_KEYS = [
        'client_id',
        'client_secret',
        'country_iso',
        'account_token',
        'refresh_token',
    ];

    /**
     * @var array
     */
    private $config;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $configFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.json';

        if (!file_exists($configFile)) {
            $this->fail("Please copy config.json.dist to config.json and set actual OLX API creds and real account token.");
        }

        $config = json_decode(file_get_contents($configFile), true);

        foreach (self::CONFIG_KEYS as $key) {
            if (!empty($config[$key])) {
                continue;
            }

            $this->fail(sprintf('Parameter `%s` should not be empty, please check config.json!', $key));
        }

        $this->config = $config;
    }

    public function testCountry()
    {
        $this->assertAttributeEquals($this->config['country_iso'], 'country', $this->getClient());
    }

    public function testCredentials()
    {
        $credentials = $this->getCredentials();

        $this->assertEquals($this->config['client_id'], $credentials->getClientId());
        $this->assertEquals($this->config['client_secret'], $credentials->getClientSecret());
    }

    /**
     * @throws OlxException
     */
    public function testToken()
    {
        $client = $this->getClient();

        $client->setRefreshToken($this->config['refresh_token']);
        $this->assertEquals($this->config['refresh_token'], $client->getRefreshToken());

        $token = $client->generateToken();
        $this->assertEquals($this->config['account_token'], $token);
        $this->assertEquals($token, $client->getToken());
    }

    /**
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testCities()
    {
        $client = $this->getClient();

        $cities = $client->cities()->list(1);
        $this->assertCount(1, $cities);

        $randomCity = current($cities);

        $this->assertArrayHasKey('id', $randomCity);
        $this->assertArrayHasKey('name', $randomCity);
        $this->assertArrayHasKey('latitude', $randomCity);
        $this->assertArrayHasKey('longitude', $randomCity);
        $this->assertArrayHasKey('region_id', $randomCity);
        $this->assertArrayHasKey('municipality', $randomCity);

        $city = $client->cities()->get($randomCity['id']);
        $this->assertArraySubset($randomCity, $city, true);

        $client->cities()->getCityDistricts($city['id']);
    }

    /**
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testUser()
    {
        $client = $this->getClient(true);

        $user = $client->user()->getMe();

        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('email', $user);
        $this->assertArrayHasKey('status', $user);
        $this->assertArrayHasKey('name', $user);
        $this->assertArrayHasKey('phone', $user);
        $this->assertArrayHasKey('created_at', $user);
        $this->assertArrayHasKey('last_login_at', $user);
        $this->assertArrayHasKey('avatar', $user);
        $this->assertArrayHasKey('is_business', $user);

        $user = $client->user()->get($user['id']);

        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('name', $user);
        $this->assertArrayHasKey('avatar', $user);

        $balance = $client->user()->getAccountBalance();

        $this->assertArrayHasKey('sum', $balance);
        $this->assertArrayHasKey('wallet', $balance);
        $this->assertArrayHasKey('bonus', $balance);
        $this->assertArrayHasKey('refund', $balance);
    }

    /**
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testCategories()
    {
        $client = $this->getClient();

        $categories = $client->categories()->list();
        $this->assertGreaterThanOrEqual(1, $categories);

        $category = current($categories);
        $this->assertArrayHasKey('id', $category);

        $attributes = $client->categories()->getAttributes($category['id']);
        $this->assertNotEmpty($attributes);

        $attribute = current($attributes);
        $this->assertArrayHasKey('code', $attribute);
        $this->assertArrayHasKey('label', $attribute);
        $this->assertArrayHasKey('unit', $attribute);
        $this->assertArrayHasKey('values', $attribute);
        $this->assertArrayHasKey('validation', $attribute);

        $validation = $attribute['validation'];
        $this->assertArrayHasKey('type', $validation);
        $this->assertArrayHasKey('required', $validation);
    }

    /**
     * @throws OlxException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testAdverts()
    {
        $client = $this->getClient(true);

        $city = $client->cities()->get(10);
        $category = $client->categories()->get(100);

        $attributes = $client->categories()->getAttributes($category['id']);

        $builder = (new AdvertBuilder())
            ->addTitle('To jest tytuł 3')
            ->addDescription('To jest opis, mój opis to jest 3')
            ->addCategoryId($category['id'])
            ->addExternalId('12345')
            ->addContact("daniel", "089123134")
            ->addLocation($city['id'], null, $city['latitude'], $city['longitude'])
            ->addPrice(12300)
        ;

        foreach ($attributes as $attribute) {
            $validation = $attribute['validation'];

            if (!$validation['required']) {
                continue;
            }

            if (!empty($attribute['values'])) {
                $value = current($attribute['values']);

                $builder->addAttribute($attribute['code'], $value['code']);
            } elseif ($validation['numeric']) {
                $builder->addAttribute($attribute['code'], $validation['min']);
            } else {
                $builder->addAttribute($attribute['code'], 'test');
            }
        }

        $advert = $client->adverts()->create($builder->getData());
        $this->assertArrayHasKey('id', $advert);

        $builder->addTitle('This is updated title');
        $builder->addDescription('This is updated description');
        $client->adverts()->update($advert['id'], $builder->getData());

        $testAdvert = $client->adverts()->get($advert['id']);
        $this->assertEquals('This is updated title', $testAdvert['title']);
        $this->assertEquals('This is updated description', $testAdvert['description']);

        $client->adverts()->delete($advert['id']);
    }

    /**
     * @return Credentials
     */
    private function getCredentials()
    {
        static $credentials;

        if (null === $credentials) {
            $credentials = new Credentials(
                $this->config['client_id'],
                $this->config['client_secret']
            );
        }

        return $credentials;
    }

    /**
     * @param bool $withAccountToken
     *
     * @return Client
     */
    private function getClient($withAccountToken = false)
    {
        static $client;

        if (null === $client) {
            try {
                $client = new Client($this->getCredentials(), $this->config['country_iso']);

                if ($withAccountToken) {
                    $client->setToken($this->config['account_token']);
                } else {
                    $client->generateToken();
                }
            } catch (OlxException $ex) {
                $this->fail($ex->getMessage());
            }
        }

        return $client;
    }
}
