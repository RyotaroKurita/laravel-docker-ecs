<?php

namespace Tests\Feature;

use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SystemCheckTest extends TestCase
{
    #[Test]
    public function トップページが200で表示される()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    #[Test]
    public function メールが送信される()
    {
        Mail::fake(); // fake は send より先に呼ぶ

        Mail::to('you@example.com')->send(new TestMail('Postfix 経由のテストです'));

        Mail::assertSent(TestMail::class, 1);

        Mail::assertSent(TestMail::class, function (TestMail $mail) {
            return $mail->hasTo('you@example.com')
                && $mail->hasSubject('Postfix メールテスト')  // ここを修正
                && $mail->assertSeeInText('Postfix 経由のテストです');
        });
    }
}