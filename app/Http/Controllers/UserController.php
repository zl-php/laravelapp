<?php
/**
 * FILE: UserController.php.
 */
namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Exceptions\InvalidRequestException;

class UserController extends Controller
{
    private $private_key = 'MIIEowIBAAKCAQEAyZGgkPRWyeGIlYEhnMccZiTZtuNJDonpL5ubAhUBq5T4JQP3nZ3COrmi1ReKxHUB4VwvqhSLd8AZhHPAt15WVPlG79QOFrAZ4tBNQWyqzvbjxsXGpNBl9HaKQBJdnbaLQF0oQqoACP+aGkUVZt+xDW6BmCKfj6tg0vZUHQZ7+2HyobG/N4bKlNYesCebbcxk46X/Y5DUs4buf57nDvzYd8JBthSMO3MTesnlqsQUBx57zK2BgzEnzMdFo1Ys8wO2UfSkcvofhgS6EJZ+7se5aLMlk3jMGEGSlexifSc7cA2vH0VjA8KdKMYLlWVKYkvJmj53VPKfli3V3AFo2r/SUwIDAQABAoIBAD7yPURHKXst9StLAiYlf9beFycn3z6tDqcRXtzNlpHwmXVlwcP06hzQr+r/yRQEviuUUEY62DrzQIS+aSZaTXeHyQFIJYYsREkyZ43Q056CNytxw9DgS5aGFjJgCeAgi2f0L4hx8kTdHVCq1j4kCPR61/mchlulVz1KM4ZE6h7cL2G1ec9697sLWpE4txoWxR1gHoYzH+FYrWftELrgMDe5Ga92zG1ohWiabvOFcki1pAFhSHG+Ymt8ZiZWPsvt6lPqqljIoYGrWzqYg6DHKkuq/RL6A5v0J+QrG2GyWZE1/gezGRODWY/tAI+TzIz1N7NapNTIXBdLoByomiCgKzECgYEA85fGYQWXvxkTkgFw9H+bBDXp0R3I1ke+BjxIR1mWbo8LxJyQAhBJNL7DSE36lTWuu3fMCZ8MoKYW3wElijz7M/egyNAdoNwkz6pDlW0l7PrA4nlqlxTZoYp/T/OirK8a+zv90CTySs6KUofDWozB9NFVtj9XpkTbXdl8Ix8+B7cCgYEA09Xl65RqltQbVxBKBhXfIaI8nquhV2amOWLN0ngSV7KcAMHzqsNn4JJZbUe3vFVNeAtdR7htbPwf7oRqKSaZ7i28E25jYiPsHNwbi+a3WZDHctW4XbdumRr/zaexGOdUbdGaeH6khz9obB5QgSyNAAg/iUdGQUvSaeWKt16qMkUCgYBF36CBDiikIV5SwGUVTVE7GPf0CzYj+TpR8ZLOHK2hExlOEZbhcKh/H8VGhU++40I29jsp+1yU7G+dEmghSjLIilytnb0R+nP4uZ9fSorZemg/zpI7ujhfNSol1f4wyJ3VuTSqMx7pRGu7FsR7weqU/kM23t6WjCPcvNj1/i096wKBgFOY2vZgCLxjEjMGm5/RK9AzHSck5jP+r2SAKGeBuQNg2g22fLmMCcdnGeF1Zv3sz/iqy3INRym2Scev+9EniaGj7M/iRVgqgvhUqI5KS1sONpeL3gkl5YCIViDLp6kDSZ9ZE1Ov7q/tBwF76RtBAJfdqW08cm48RNpDmr2InzPdAoGBANsayzEUR3pWaXrjvkBa/Rq+G2lJaUhtGsjxg2jzSSQ42pUgYtKH9y5P+DzNFzCHDGpaHQuCSmr6KCsJdLcVTwLjCrtssnbSZUhkBGvVLnW+5pyCtZF0BRTwo0F0TyrOQS5ej8A/1kEUVbfYSik48Z17C3vZEvB4UnPgtEYcSb3L';
    private $public_key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyZGgkPRWyeGIlYEhnMccZiTZtuNJDonpL5ubAhUBq5T4JQP3nZ3COrmi1ReKxHUB4VwvqhSLd8AZhHPAt15WVPlG79QOFrAZ4tBNQWyqzvbjxsXGpNBl9HaKQBJdnbaLQF0oQqoACP+aGkUVZt+xDW6BmCKfj6tg0vZUHQZ7+2HyobG/N4bKlNYesCebbcxk46X/Y5DUs4buf57nDvzYd8JBthSMO3MTesnlqsQUBx57zK2BgzEnzMdFo1Ys8wO2UfSkcvofhgS6EJZ+7se5aLMlk3jMGEGSlexifSc7cA2vH0VjA8KdKMYLlWVKYkvJmj53VPKfli3V3AFo2r/SUwIDAQAB';

    public function encrypt()
    {
        $str = 123456789;

        //验证公钥 拼装公钥
        if (Str::endsWith($this->public_key, '.pem')) {
            $public_key = openssl_pkey_get_public( file_get_contents($this->public_key) );;
        } else {
            $public_key = "-----BEGIN PUBLIC KEY-----\n".
                wordwrap($this->public_key, 64, "\n", true).
                "\n-----END PUBLIC KEY-----";
        }

        if (is_resource($public_key))
            openssl_free_key($public_key);

        try {
            openssl_public_encrypt($str,$encrypted, $public_key);

            // base64_encode转码后的内容通常含有特殊字符，在浏览器通过url传输时要注意base64编码是否是url安全的，所以进行url转码
            $encrypted = urlencode(base64_encode($encrypted));

            return $encrypted;

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function decrypt()
    {
        $str = 'lj73ktX7FJWb534rbiEC%2BoMqlpDcbnL%2FvH6bA79kAmEY35%2Bhd3Wz%2BPofoKRRRIPYuZ6%2FCkHk5urzjfMwyOWlfeCZGyLpsZ6SGQU8Tf2OV13qSxrEG50wKVAm5VApi96IrFZfXC3ChYZZv9ilzqQH6uTyhkXHrg3Gi4vfXNTPUTtGzPKg0RYdQjTd9aIBlNAy60mMB4au%2BSanMmhwh1%2FA2AvqvcRiQQL154Npcs4%2FGwEUAyqaWBWwMo3oNtnNC71KBFidt9PE0sy2Iy3WhnGbhFKmygpqR6%2Bhfg0k454qMX60o68KDrXHYI12hSzUQoaCb4jTywXVG3FDj%2Bv1uU1aUA%3D%3D';

        //验证公钥 拼装公钥
        if (Str::endsWith($this->private_key, '.pem')) {
            $private_key = openssl_pkey_get_private($this->private_key);
        } else {
            $private_key = "-----BEGIN RSA PRIVATE KEY-----\n".
                wordwrap($this->private_key, 64, "\n", true).
                "\n-----END RSA PRIVATE KEY-----";
        }

        if (is_resource($private_key))
            openssl_free_key($private_key);

        try {
            openssl_private_decrypt(base64_decode(urldecode($str)), $decrypted, $private_key);

            return $decrypted;

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    // 私钥签名
    public function genSign()
    {
        // 待生成签名的字符串
        $str = 'a=1&b=2&c=3&d=5';

        // 验证私钥 拼装私钥
        if (Str::endsWith($this->private_key, '.pem')) {
            $private_key = openssl_pkey_get_private($this->private_key);
        } else {
            $private_key = "-----BEGIN RSA PRIVATE KEY-----\n".
                wordwrap($this->private_key, 64, "\n", true).
                "\n-----END RSA PRIVATE KEY-----";
        }

        if (is_resource($private_key))
            openssl_free_key($private_key);

        try {
            openssl_sign($str, $signature, $private_key);

            return base64_encode($signature);

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    // 公钥验签
    public function verifySign()
    {
        // 获取到参与签名的字符串
        $str = 'a=1&b=2&c=3&d=5';
        // 签名
        $sign = 'ZSMivQqMFZ1s36NFE9kcB83BcltwIIxZF+l1tbaS8KJE7Ln+CjaklFu4tC9MepAJBAElKw9dbMq2/uYuQ6IFgC9bjDpZeimAUUgdQMqWvFTzzY5d0nPa6RsDvz5lhpRBW7tOnEcLoC+W1bZnwxigrWQr5Ya4KSnj5UYXuMRwhJ8JqSDS9hFJMQIIsB9v01r9hYtdTAyD5vq9ltRasGPe34u2fQhtMHjSeyvB5KZD6UVQRbJbeSxSoiJVhfLqgcyLL12Amn2x4qogUkUVIYMJkJ1WU5imJOthUlM4wqPWFbNAWdOAUbutm6rGhE+rfsL/UbYV+xBnl3OVHPrBe25D6g==';

        // 验证公钥 拼装公钥
        if (Str::endsWith($this->public_key, '.pem')) {
            $publicKey = openssl_pkey_get_public($this->public_key);
        } else {
            $publicKey = "-----BEGIN PUBLIC KEY-----\n".
                wordwrap($this->public_key, 64, "\n", true).
                "\n-----END PUBLIC KEY-----";
        }

        if (is_resource($publicKey))
            openssl_free_key($publicKey);

        // 验签
        try {
            // 如果签名正确返回 1, 签名错误返回 0, 内部发生错误则返回-1
            $result = openssl_verify($str, base64_decode($sign), $publicKey);

            return $result;

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function test()
    {
        $data = 'zhoulei';

        $pr = storage_path('apiclient_key.pem');

        if (Str::endsWith($pr, '.pem')) {


            $privateKey = openssl_pkey_get_private( file_get_contents($pr));

                    $bool = openssl_private_encrypt($data, $encrypted, $privateKey);

        $encrypted = urlencode(base64_encode($encrypted));

        dd($encrypted);
        }

//        $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n".
//            wordwrap($this->private_key, 64, "\n", true).
//            "\n-----END RSA PRIVATE KEY-----";
//
//        if (is_resource($privateKey))
//            openssl_free_key($privateKey);
//
//        $bool = openssl_private_encrypt($data, $encrypted, $privateKey);
//
//        $encrypted = urlencode(base64_encode($encrypted));
//
//        dd($encrypted);



//        $response = Http::withHeaders(['Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9uaWVsc2VuaXEuY29tXC9hcGlcL2F1dGhcL290aGVyXC9sb2dpbiIsImlhdCI6MTY0ODcxOTAxNywiZXhwIjoxNjUwMDE1MDE3LCJuYmYiOjE2NDg3MTkwMTcsImp0aSI6InVIS0FOWUswcjV0SmlyRmciLCJzdWIiOjEyMywicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.ejvtrxt544qDihOiiCbwVmzsukD4-4l-oC6A5_DMnzg'])
//            ->post('nielseniq.com/api/live/get', [
//                'id' => 4
//            ]);
//
//
//        return json_decode($response->getBody(), true);

    }

    public function test1()
    {
        return $this->success();

    }
}