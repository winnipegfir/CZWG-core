<?php

namespace Tests\Unit;

use App\Services\VatcanService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VatcanPublicRosterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('vatcan.api_key', null);
        config()->set('vatcan.public_roster_url', 'https://vatcan.test/division/facility/CZWG');
        config()->set('vatcan.public_roster_min_members', 2);
    }

    public function test_it_reads_home_controllers_and_keeps_visitors_separate(): void
    {
        Http::fake([
            'https://vatcan.test/division/facility/CZWG' => Http::response($this->validRosterHtml(), 200),
        ]);

        $result = (new VatcanService)->getRoster();

        $this->assertSame('ok', $result['status']);
        $this->assertSame('public_roster', $result['source']);
        $this->assertSame([1234567, 2345678], array_column($result['data']['controllers'], 'cid'));
        $this->assertSame([2, 5], array_column($result['data']['controllers'], 'rating'));
        $this->assertSame([3456789], array_column($result['data']['visitors'], 'cid'));
    }

    public function test_academy_can_request_the_public_roster_when_the_api_is_configured(): void
    {
        config()->set('vatcan.api_key', 'test-key');
        config()->set('vatcan.api_url', 'https://vatcan.test/api/v2');

        Http::fake([
            'https://vatcan.test/division/facility/CZWG' => Http::response($this->validRosterHtml(), 200),
        ]);

        $result = (new VatcanService)->getPublicRoster();

        $this->assertSame('ok', $result['status']);
        $this->assertSame('public_roster', $result['source']);
        $this->assertCount(2, $result['data']['controllers']);
    }

    public function test_the_existing_shared_roster_method_still_prefers_the_api(): void
    {
        config()->set('vatcan.api_key', 'test-key');
        config()->set('vatcan.api_url', 'https://vatcan.test/api/v2');

        Http::fake([
            'https://vatcan.test/api/v2/facility/roster' => Http::response([
                'data' => [
                    'controllers' => [
                        ['cid' => 1234567, 'rating' => 2],
                        ['cid' => 2345678, 'rating' => 5],
                    ],
                    'visitors' => [],
                ],
            ], 200),
        ]);

        $result = (new VatcanService)->getRoster();

        $this->assertSame('ok', $result['status']);
        $this->assertSame('api', $result['source']);
    }

    public function test_it_skips_suspended_home_members_without_rejecting_the_roster(): void
    {
        $html = str_replace(
            '<tr><td>2345678</td><td>Casey Controller</td><td>Controller 1</td></tr>',
            '<tr><td>2345678</td><td>Casey Controller</td><td>Controller 1</td></tr>'.
            '<tr><td>4567890</td><td>Suspended Person</td><td>Suspended</td></tr>',
            $this->validRosterHtml()
        );

        $result = (new VatcanService)->parsePublicRoster($html);

        $this->assertSame('ok', $result['status']);
        $this->assertSame([1234567, 2345678], array_column($result['data']['controllers'], 'cid'));
    }

    public function test_it_rejects_a_changed_table_instead_of_returning_an_empty_roster(): void
    {
        $html = '<html><body><h4>Home Controllers</h4><table><thead><th>Member</th></thead><tbody><tr><td>1234567</td></tr></tbody></table></body></html>';

        $result = (new VatcanService)->parsePublicRoster($html);

        $this->assertSame('error', $result['status']);
        $this->assertStringContainsString('expected CID, Name, and Rating', $result['message']);
    }

    public function test_it_rejects_an_unexpectedly_small_roster(): void
    {
        config()->set('vatcan.public_roster_min_members', 3);

        $result = (new VatcanService)->parsePublicRoster($this->validRosterHtml());

        $this->assertSame('error', $result['status']);
        $this->assertStringContainsString('expected at least 3', $result['message']);
    }

    public function test_it_rejects_a_missing_visitor_table_to_protect_existing_access(): void
    {
        $html = <<<'HTML'
<html><body><h4>Home Controllers</h4><table><thead><th>CID</th><th>Name</th><th>Rating</th></thead><tbody>
<tr><td>1234567</td><td>Alex Student</td><td>Student 1</td></tr>
<tr><td>2345678</td><td>Casey Controller</td><td>Controller 1</td></tr>
</tbody></table></body></html>
HTML;

        $result = (new VatcanService)->parsePublicRoster($html);

        $this->assertSame('error', $result['status']);
        $this->assertStringContainsString('Visiting Controllers table was not found', $result['message']);
    }

    private function validRosterHtml(): string
    {
        return <<<'HTML'
<!doctype html><html><body>
<h4>Home Controllers</h4>
<table><thead><th>CID</th><th>Name</th><th>Rating</th></thead><tbody>
<tr><td>1234567</td><td>Alex Student</td><td>Student 1</td></tr>
<tr><td>2345678</td><td>Casey Controller</td><td>Controller 1</td></tr>
</tbody></table>
<h4>Visiting Controllers</h4>
<table><thead><tr><th>CID</th><th>Name</th><th>Rating</th></tr></thead><tbody>
<tr><td>3456789</td><td>Visitor Person</td><td>Student 2</td></tr>
</tbody></table>
</body></html>
HTML;
    }
}
