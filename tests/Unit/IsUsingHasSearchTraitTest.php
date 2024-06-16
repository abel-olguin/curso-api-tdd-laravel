<?php

namespace Tests\Unit;

use App\Models\Menu;
use App\Models\Plate;
use App\Models\Restaurant;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class IsUsingHasSearchTraitTest extends TestCase
{
    #[Test]
    public function restaurant_model_is_using_the_has_search_trait(): void
    {
        $restaurant = new Restaurant();

        $this->assertTrue(method_exists($restaurant, 'scopeSearch'));
    }

    #[Test]
    public function plate_model_is_using_the_has_search_trait(): void
    {
        $restaurant = new Plate();

        $this->assertTrue(method_exists($restaurant, 'scopeSearch'));
    }

    #[Test]
    public function menu_model_is_using_the_has_search_trait(): void
    {
        $restaurant = new Menu();

        $this->assertTrue(method_exists($restaurant, 'scopeSearch'));
    }
}
