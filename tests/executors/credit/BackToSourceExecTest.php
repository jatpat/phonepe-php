<?php
/**
 * Created by PhpStorm.
 * User: jigar.thakkar
 * Date: 08/11/16
 * Time: 7:12 PM
 */

use InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;
use PhonePe\Models\BackToSourceCreditRequest;
use PhonePe\Models\Header;
use PhonePe\Models\Salt;
use PhonePe\PhonePeClientImpl;
use PHPUnit\Framework\TestCase;
use WireMock\Client\WireMock;

require __DIR__ . '/../../../vendor/autoload.php';

class BackToSourceExecTest extends TestCase
{
    use HttpMockTrait;
    public static $host = 'localhost';
    public static $port = '8080';

    public static function setUpBeforeClass()
    {
        static::setUpHttpMockBeforeClass(self::$port, self::$host);
    }

    public static function tearDownAfterClass()
    {
        static::tearDownHttpMockAfterClass();
    }

    public function setUp()
    {
        $this->setUpHttpMock();
    }

    public function tearDown()
    {
        $this->tearDownHttpMock();
    }

    public function testBackToSource() {
        $mockResponse = '{ "success": true, "code": "PAYMENT_SUCCESS", "message": "Your payment is successful.", "data": { "transactionId": "TX123456789", "merchantId": "DemoMerchant", "amount": 100, "status": "SUCCESS", "merchantUserId": "U123456789", "mobileNumber": "9xxxxxxxxxx", "providerReferenceId": "PPXXXXX", "payResponseCode": "SUCCESS" } }';
        $this->http->mock
            ->when()
                ->methodIs('POST')
                ->pathIs('/v1/credit/backToSource')
            ->then()
                ->body($mockResponse)
            ->end();
        $this->http->setUp();

        $testRequest = new BackToSourceCreditRequest();
        $testRequest->header = new Header();
        $testRequest->header->salt = new Salt();
        $testRequest->header->salt->key = "saltKey";
        $testRequest->header->salt->index = 1;
        $testRequest->amount = 100;
        $testRequest->merchantId = 'DemoMerchant';
        $testRequest->transactionId = 'TX123456789';
        $testRequest->merchantUserId = "U123456789";
        $testRequest->providerReferenceId = "PAEWE12334";
        $testRequest->mobileNumber = "9xxxxxxxxxx";
        $testRequest->shortName = "Demo";
        $testRequest->merchantOrderId = "O1234";
        $testRequest->email = "test@phonepe.com";
        $testRequest->message = "Test Payment";
        $testRequest->subMerchant = "Sub-Merchant";
        $client = PhonePeClientImpl::testConstruct('http://' . self::$host . ':' . self::$port);
        $result = $client->backToSourceCredit($testRequest);

        $this->assertEquals($result->success, true);
        $this->assertEquals($result->code, "PAYMENT_SUCCESS");
        $this->assertEquals($result->data->status, "SUCCESS");
    }
}