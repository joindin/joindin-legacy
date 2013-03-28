<?php
namespace JoindinTest\Inc;

require_once __DIR__ . '/../../inc/Request.php';

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that if a parameter was sent in, calling getParameter for it will
     * return the value it was set to.
     *
     * @return void
     *
     * @test
     * @backupGlobals
     */
    public function getParameterReturnsValueOfRequestedParameter()
    {
        $queryString = http_build_query(
            array(
                 'foo' => 'bar',
                 'baz' => 'samoflange',
            )
        );

        $_SERVER['QUERY_STRING'] = $queryString;
        $request                 = new \Request();

        $this->assertEquals('bar', $request->getParameter('foo'));
        $this->assertEquals('samoflange', $request->getParameter('baz'));
    }

    /**
     * Ensures that getParameter returns the default value if the parameter requested
     * was not set.
     *
     * @return void
     *
     * @test
     */
    public function getParameterReturnsDefaultIfParameterNotSet()
    {
        $uniq    = uniqid();
        $request = new \Request();
        $result  = $request->getParameter('samoflange', $uniq);

        $this->assertSame($uniq, $result);
    }

    /**
     * Ensures that methods are properly loaded from the
     * $_SERVER['REQUEST_METHOD'] variable
     *
     * @param string $method Method to try
     *
     * @return void
     *
     * @test
     * @dataProvider methodProvider
     * @backupGlobals
     */
    public function requestMethodIsProperlyLoaded($method)
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $request                   = new \Request();

        $this->assertEquals($method, $request->getVerb());
    }

    /**
     * Ensures that a verb can be set on the request with setVerb
     *
     * @param string $verb Verb to set
     *
     * @return void
     *
     * @test
     * @dataProvider methodProvider
     */
    public function setVerbAllowsForSettingRequestVerb($verb)
    {
        $request = new \Request();
        $request->setVerb($verb);

        $this->assertEquals($verb, $request->getVerb());
    }

    /**
     * Ensure the setVerb method is fluent
     *
     * @return void
     *
     * @test
     */
    public function setVerbIsFluent()
    {
        $request = new \Request();
        $this->assertSame($request, $request->setVerb(uniqid()));
    }

    /**
     * Provides a list of valid HTTP verbs to test with
     *
     * @return array
     */
    public function methodProvider()
    {
        return array(
            array('GET'),
            array('POST'),
            array('PUT'),
            array('DELETE'),
            array('TRACE'),
            array('HEAD'),
            array('OPTIONS')
        );
    }

    /**
     * Ensures that the default value is returned if the requested index is
     * not found on getUrlElement
     *
     * @return void
     *
     * @test
     */
    public function getUrlElementReturnsDefaultIfIndexIsNotFound()
    {
        $request = new \Request();

        $default = uniqid();
        $result  = $request->getUrlElement(22, $default);

        $this->assertEquals($default, $result);
    }

    /**
     * Ensures that url elements can be properly fetched with a call to
     * getUrlElement
     *
     * @return void
     *
     * @test
     * @backupGlobals
     */
    public function getUrlElementReturnsRequestedElementFromPath()
    {
        $_SERVER['PATH_INFO'] = 'foo/bar/baz';
        $request              = new \Request();
        $this->assertEquals('foo', $request->getUrlElement(0));
        $this->assertEquals('bar', $request->getUrlElement(1));
        $this->assertEquals('baz', $request->getUrlElement(2));
    }

    /**
     * Ensures the accept headers are properly parsed
     *
     * @return void
     *
     * @test
     * @backupGlobals
     */
    public function acceptsHeadersAreParsedCorrectly()
    {
        $_SERVER['HTTP_ACCEPT'] =
            'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $request                = new \Request();

        $this->assertFalse($request->accepts('image/png'));
        $this->assertTrue($request->accepts('text/html'));
        $this->assertTrue($request->accepts('application/xhtml+xml'));
        $this->assertTrue($request->accepts('application/xml'));
        $this->assertTrue($request->accepts('*/*'));
    }

    /**
     * Ensures that if we're accepting something that the accept headers
     * say, then we get back that format
     *
     * @return void
     *
     * @test
     * @backupGlobals
     */
    public function preferredContentTypeOfReturnsADesiredFormatIfItIsAccepted()
    {
        $_SERVER['HTTP_ACCEPT'] =
            'text/text,application/xhtml+xml,application/json;q=0.9,*/*;q=0.8';
        $request                = new \Request();

        $result = $request->preferredContentTypeOutOf(
            array('text/html', 'application/json')
        );

        $this->assertEquals('application/json', $result);
    }

    /**
     * Ensures that if the browser doesn't send an accept header we can deal with
     * we return json
     *
     * @return void
     *
     * @test
     * @backupGlobals
     */
    public function ifPreferredFormatIsNotAcceptedReturnJson()
    {
        $_SERVER['HTTP_ACCEPT'] =
            'text/text,application/xhtml+xml,application/json;q=0.9,*/*;q=0.8';
        $request                = new \Request();

        $result = $request->preferredContentTypeOutOf(
            array('text/html'),
            array('application/xml')
        );

        $this->assertEquals('json', $result);
    }

    /**
     * Ensures host is set correctly from headers
     *
     * @return void
     *
     * @test
     * @backupGlobals
     */
    public function hostIsSetCorrectlyFromTheHeaders()
    {
        $_SERVER['HTTP_HOST'] = 'joind.in';
        $request              = new \Request();

        $this->assertEquals('joind.in', $request->host);
        $this->assertEquals('joind.in', $request->getHost());
    }

    /**
     * Ensures that the setHost method is fluent
     *
     * @return void
     *
     * @test
     */
    public function setHostIsFluent()
    {
        $request = new \Request();
        $this->assertSame($request, $request->setHost(uniqid()));
    }

    /**
     * Ensures that setHost can be used to set a host
     *
     * @return void
     *
     * @test
     */
    public function hostCanBeSetWithSetHost()
    {
        $host    = uniqid() . '.com';
        $request = new \Request();
        $request->setHost($host);

        $this->assertEquals($host, $request->getHost());
    }

    /**
     * Ensures that if a json body is provided on a POST or PUT request, it
     * gets parsed as parameters
     *
     * @param string $method Method to use
     *
     * @return void
     *
     * @test
     * @dataProvider postPutProvider
     * @backupGlobals
     */
    public function jsonBodyIsParsedAsParameters($method)
    {
        $body = json_encode(
            array(
                 'a'     => 'b',
                 'array' => array('joind' => 'in')
            )
        );

        $inside        = new \stdClass();
        $inside->joind = 'in';

        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['CONTENT_TYPE']   = 'application/json';
        /* @var $request \Request */
        $request = $this->getMock('\Request', array('getRawBody'), array(false));
        $request->expects($this->once())
            ->method('getRawBody')
            ->will($this->returnValue($body));

        $request->setVerb($method);
        $request->parseParameters();

        $this->assertEquals('b', $request->getParameter('a'));
        $this->assertEquals($inside, $request->getParameter('array'));
    }

    /**
     * Provider for methods
     *
     * @return array
     */
    public function postPutProvider()
    {
        return array(
            array('POST'),
            array('PUT')
        );
    }

    /**
     * Ensures that the scheme is set to http unless https is on
     *
     * @return void
     *
     * @test
     */
    public function schemeIsHttpByDefault()
    {
        $request = new \Request();

        $this->assertEquals('http://', $request->scheme);
        $this->assertEquals('http://', $request->getScheme());
    }

    /**
     * Ensures that the scheme is set to https:// if the HTTPS value is
     * set to 'on'
     *
     * @return void
     *
     * @test
     * @backupGlobals
     */
    public function schemeIsHttpsIfHttpsValueIsOn()
    {
        $_SERVER['HTTPS'] = 'on';
        $request          = new \Request();

        $this->assertEquals('https://', $request->scheme);
        $this->assertEquals('https://', $request->getScheme());
    }

    /**
     * Ensures setScheme provides a fluent interface
     *
     * @return void
     *
     * @test
     */
    public function setSchemeIsFluent()
    {
        $request = new \Request();
        $this->assertSame($request, $request->setScheme('http://'));
    }

    /**
     * Ensures that the scheme can be set by the set scheme method
     *
     * @param string $scheme Scheme to set
     *
     * @return void
     *
     * @test
     * @dataProvider schemeProvider
     */
    public function schemeCanBeSetBySetSchemeMethod($scheme)
    {
        $request = new \Request();
        $request->setScheme($scheme);

        $this->assertEquals($scheme, $request->getScheme());
    }

    /**
     * Provides schemes for tests
     *
     * @return array
     */
    public function schemeProvider()
    {
        return array(
            array('http://'),
            array('https://'),
        );
    }

    /**
     * Ensures that an exception is thrown if the authorization header
     * doesn't have two parts
     *
     * @return void
     *
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid Authorization Header
     * @expectedExceptionCode 400
     */
    public function ifIdentificationDoesNotHaveTwoPartsExceptionIsThrown()
    {
        $request = new \Request();
        $request->identifyUser(null, 'This is a bad header');
    }

    /**
     * Ensures that an exception is thrown if the authorization header doesn't
     * start with oauth
     *
     * @return void
     *
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown Authorization Header Received
     * @expectedExceptionCode 400
     */
    public function ifIdentificationHeaderDoesNotStartWithOauthThrowException()
    {
        $request = new \Request();
        $request->identifyUser(null, 'Auth Me');
    }

    /**
     * Ensures that if getOAuthModel is called, an instance of OAuthModel
     * is returned
     *
     * @return void
     *
     * @test
     */
    public function getOauthModelProvidesAnOauthModel()
    {
        // Please see below for explanation of why we're mocking a "mock" PDO
        // class
        $db      = $this->getMock(
            '\JoindinTest\Inc\mockPDO',
            array('getAvailableDrivers')
        );
        $request = new \Request();
        $result  = $request->getOAuthModel($db);

        $this->assertInstanceOf('OAuthModel', $result);
    }

    /**
     * Ensures that if the getOauthModel method is called and no model is already
     * set, and no PDO adapter is provided, an exception is thrown
     *
     * @return void
     *
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Db Must be provided to get Oauth Model
     */
    public function callingGetOauthModelWithoutADatabaseAdapterThrowsAnException()
    {
        $request = new \Request();
        $request->getOauthModel();
    }

    /**
     * Ensures that the setOauthModel method is fluent
     *
     * @return void
     *
     * @test
     */
    public function setOauthModelMethodIsFluent()
    {
        /* @var $mockOauth \OAuthModel */
        $mockOauth = $this->getMock('OAuthModel', array(), array(), '', false);
        $request   = new \Request();

        $this->assertSame($request, $request->setOauthModel($mockOauth));
    }

    /**
     * Ensures that the setOauthModel method allows for an OAuthModel
     * to be set and retrieved
     *
     * @return void
     *
     * @test
     */
    public function setOauthModelAllowsSettingOfOauthModel()
    {
        /* @var $mockOauth \OAuthModel */
        $mockOauth = $this->getMock('OAuthModel', array(), array(), '', false);
        $request   = new \Request();
        $request->setOauthModel($mockOauth);

        $this->assertSame($mockOauth, $request->getOauthModel());
    }

    /**
     * Ensures that identifyUser method sets a user id on the request model
     *
     * @return void
     *
     * @test
     */
    public function identifyUserSetsUserIdForValidHeader()
    {
        $request   = new \Request();
        $mockOauth = $this->getMock('OAuthModel', array(), array(), '', false);
        $mockOauth->expects($this->once())
            ->method('verifyAccessToken')
            ->with('authPart')
            ->will($this->returnValue('TheUserId'));

        $request->setOauthModel($mockOauth);

        $request->identifyUser(null, 'oauth authPart');

        $this->assertEquals('TheUserId', $request->user_id);
        $this->assertEquals('TheUserId', $request->getUserId());
    }

    /**
     * Ensures that the setUserId method is fluent
     *
     * @return void
     *
     * @test
     */
    public function setUserIdIsFluent()
    {
        $request = new \Request();
        $this->assertSame($request, $request->setUserId('TheUserToSet'));
    }

    /**
     * Ensures that setUserId can set a user id into the model that can be
     * retrieved with getUserId
     *
     * @return void
     *
     * @test
     */
    public function setUserIdAllowsForSettingOfUserId()
    {
        $request = new \Request();
        $user    = uniqid();

        $request->setUserId($user);
        $this->assertEquals($user, $request->getUserId());
    }

    /**
     * Ensures the setPathInfo method allows setting of a path
     *
     * @return void
     *
     * @test
     */
    public function setPathInfoAllowsSettingOfPathInfo()
    {
        $path    = uniqid() . '/' . uniqid() . '/' . uniqid();
        $parts   = explode('/', $path);
        $request = new \Request();
        $request->setPathInfo($path);

        $this->assertEquals($path, $request->getPathInfo());
        $this->assertEquals($path, $request->path_info);

        $this->assertEquals($parts[0], $request->getUrlElement(0));
        $this->assertEquals($parts[1], $request->getUrlElement(1));
        $this->assertEquals($parts[2], $request->getUrlElement(2));
    }

    /**
     * Ensures the setPath method is fluent
     *
     * @return void
     *
     * @test
     */
    public function setPathIsFluent()
    {
        $request = new \Request();
        $this->assertSame($request, $request->setPathInfo(uniqid()));
    }

    /**
     * Ensures the setAccept header sets the accept variable
     *
     * @return void
     *
     * @test
     */
    public function setAcceptSetsTheAcceptVariable()
    {
        $accept      = uniqid() . ',' . uniqid() . ',' . uniqid();
        $acceptParts = explode(',', $accept);

        $request = new \Request();
        $request->setAccept($accept);
        $this->assertEquals($acceptParts, $request->accept);

        foreach ($acceptParts as $thing) {
            $this->assertTrue($request->accepts($thing));
        }
    }

    /**
     * Ensures that the setAccept method is fluent
     *
     * @return void
     *
     * @test
     */
    public function setAcceptsIsFluent()
    {
        $request = new \Request();
        $this->assertSame($request, $request->setAccept(uniqid()));
    }

    /**
     * Ensures the setBase method allows setting of the base variable
     *
     * @return void
     *
     * @test
     */
    public function setBaseAllowsSettingOfBase()
    {
        $request = new \Request();
        $base = uniqid();
        $request->setBase($base);
        $this->assertEquals($base, $request->getBase());
        $this->assertEquals($base, $request->base);
    }

    /**
     * Ensures the setBase method is fluent
     *
     * @return void
     *
     * @test
     */
    public function setBaseIsFluent()
    {
        $request = new \Request();
        $this->assertSame($request, $request->setBase(uniqid()));
    }
}

/**
 * Class to allow for mocking PDO to send to the OAuthModel
 */
class mockPDO extends \PDO
{
    /**
     * Constructor that does nothing but helps us test with fake database
     * adapters
     */
    public function __construct()
    {
        // We need to do this crap because PDO has final on the __sleep and
        // __wakeup methods. PDO requires a parameter in the constructor but we don't
        // want to create a real DB adapter. If you tell getMock to not call the
        // original constructor, it fakes stuff out by unserializing a fake
        // serialized string. This way, we've got a "PDO" object but we don't need
        // PHPUnit to fake it by unserializing a made-up string. We've neutered
        // the constructor in mockPDO.
    }
}
