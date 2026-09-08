<?php

namespace Tests\Unit;

use App\Services\AcademyVatcanSyncService;
use Tests\TestCase;

class AcademyVisitorEntitlementTest extends TestCase
{
    public function test_visitors_below_s3_receive_no_courses(): void
    {
        $slugs = (new AcademyVatcanSyncService)->visitorEntitlementSlugsForRating(3, ['visiting-controller-orientation']);

        $this->assertSame([], $slugs);
    }

    public function test_s3_visitors_receive_the_s3_ladder_and_visitor_course(): void
    {
        $slugs = (new AcademyVatcanSyncService)->visitorEntitlementSlugsForRating(4, ['visiting-controller-orientation']);

        $this->assertContains('terminal', $slugs);
        $this->assertContains('visiting-controller-orientation', $slugs);
        $this->assertNotContains('center', $slugs);
    }

    public function test_c1_visitors_receive_center_and_visitor_course(): void
    {
        $slugs = (new AcademyVatcanSyncService)->visitorEntitlementSlugsForRating(5, ['visiting-controller-orientation']);

        $this->assertContains('center', $slugs);
        $this->assertContains('visiting-controller-orientation', $slugs);
    }
}
