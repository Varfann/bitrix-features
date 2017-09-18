<?

/**
 * Some examples to use Curl and Multicurl wrappers
 *
 * @see more examples on <https://github.com/php-curl-class/php-curl-class/tree/master/examples>
 */

use Curl\Curl, \Curl\MultiCurl;

$curl = new Curl();
$curl->get('http://www.example.com/');

$curl = new Curl();

$curl->get('http://www.example.com/search', array(
    'q' => 'keyword',
));

$curl = new Curl();
$curl->post('http://www.example.com/login/', array(
    'username' => 'myusername',
    'password' => 'mypassword',
));

// Perform a post-redirect-get request (POST data and follow 303 redirections using GET requests).
$curl = new Curl();
$curl->setOpt(CURLOPT_FOLLOWLOCATION, true);

$curl->post('http://www.example.com/login/', array(
    'username' => 'myusername',
    'password' => 'mypassword',
));

// POST data and follow 303 redirections by POSTing data again.
// Please note that 303 redirections should not be handled this way:
// https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.3.4
$curl = new Curl();
$curl->setOpt(CURLOPT_FOLLOWLOCATION, true);¬
$curl->post('http://www.example.com/login/', array(
    'username' => 'myusername',
    'password' => 'mypassword',
), false);

$curl = new Curl();
$curl->setBasicAuthentication('username', 'password');
$curl->setUserAgent('');
$curl->setReferrer('');
$curl->setHeader('X-Requested-With', 'XMLHttpRequest');
$curl->setCookie('key', 'value');
$curl->get('http://www.example.com/');

if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage;
} else {
    echo $curl->response;
}

var_dump($curl->requestHeaders);
var_dump($curl->responseHeaders);

$curl = new Curl();
$curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
$curl->get('https://encrypted.example.com/');

$curl = new Curl();
$curl->put('http://api.example.com/user/', array(
    'first_name' => 'Zach',
    'last_name'  => 'Borboa',
));

$curl = new Curl();
$curl->patch('http://api.example.com/profile/', array(
    'image' => '@path/to/file.jpg',
));

$curl = new Curl();
$curl->patch('http://api.example.com/profile/', array(
    'image' => new CURLFile('path/to/file.jpg'),
));

$curl = new Curl();
$curl->delete('http://api.example.com/user/', array(
    'id' => '1234',
));
// Enable gzip compression and download a file.

$curl = new Curl();
$curl->setOpt(CURLOPT_ENCODING, 'gzip');
$curl->download('https://www.example.com/image.png', '/tmp/myimage.png');
// Case-insensitive access to headers.

$curl = new Curl();
$curl->download('https://www.example.com/image.png', '/tmp/myimage.png');
echo $curl->responseHeaders['Content-Type'] . "\n"; // image/png
echo $curl->responseHeaders['CoNTeNT-TyPE'] . "\n"; // image/png

$curl->close();
// Example access to curl object.
curl_set_opt($curl->curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1');
curl_close($curl->curl);

// Requests in parallel with callback functions.
$multi_curl = new MultiCurl();

$multi_curl->success(function ($instance) {
    echo 'call to "' . $instance->url . '" was successful.' . "\n";
    echo 'response: ' . $instance->response . "\n";
});

$multi_curl->error(function ($instance) {
    echo 'call to "' . $instance->url . '" was unsuccessful.' . "\n";
    echo 'error code: ' . $instance->errorCode . "\n";
    echo 'error message: ' . $instance->errorMessage . "\n";
});

$multi_curl->complete(function ($instance) {
    echo 'call completed' . "\n";
});

$multi_curl->addGet('https://www.google.com/search', array(
    'q' => 'hello world',
));

$multi_curl->addGet('https://duckduckgo.com/', array(
    'q' => 'hello world',
));

$multi_curl->addGet('https://www.bing.com/search', array(
    'q' => 'hello world',
));

$multi_curl->start(); // Blocks until all items in the queue have been processed.