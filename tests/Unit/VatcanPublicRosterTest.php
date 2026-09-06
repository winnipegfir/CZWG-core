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
