<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds in the correct order.
     *
     * Order matters — seeders with FKs must run after their parents.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,         // 1. admins first (no deps)
            CategorySeeder::class,      // 2. categories (no deps)
            ProductSeeder::class,       // 3. products (needs categories)
            InventorySeeder::class,     // 4. inventory (no deps)
            CustomerSeeder::class,      // 5. customers (no deps)
            OrderSeeder::class,         // 6. orders (needs customers + products)
            SaleSeeder::class,          // 7. sales (needs admins + customers + orders + products)
            AnnouncementSeeder::class,  // 8. announcements (needs admins)
            NotificationSeeder::class,  // 9. notifications (needs customers + admins + orders + announcements)
        ]);
    }
}