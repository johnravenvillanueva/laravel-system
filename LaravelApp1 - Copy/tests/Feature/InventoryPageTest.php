<?php

namespace Tests\Feature;

use Tests\TestCase;

class InventoryPageTest extends TestCase
{
    public function test_home_page_displays_inventory_management():
 {

        $response = $this->get('/home');

        $response->assertStatus(200);
        $response->assertSee('Inventory');
        $response->assertSee('Management');
        $response->assertSee('Add Item');
 }
}
