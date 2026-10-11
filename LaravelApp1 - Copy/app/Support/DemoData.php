<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class DemoData
{
    public static function categories(): Collection
    {
        return collect([
            (object)['id'=>1,'name'=>'Coffee','description'=>'Espresso-based drinks','products_count'=>6],
            (object)['id'=>2,'name'=>'Non-Coffee','description'=>'Matcha, chocolate, and more','products_count'=>4],
            (object)['id'=>3,'name'=>'Pastry','description'=>'Freshly baked daily','products_count'=>5],
        ]);
    }

    public static function products(): Collection
    {
        return collect([
            (object)['id'=>1,'name'=>'Issang Macchiato','description'=>'Our signature caramel macchiato','price'=>145.00,'category_id'=>1,'is_available'=>true,'category'=>(object)['name'=>'Coffee']],
            (object)['id'=>2,'name'=>'Spanish Latte','description'=>'Creamy, sweet, bold','price'=>135.00,'category_id'=>1,'is_available'=>true,'category'=>(object)['name'=>'Coffee']],
            (object)['id'=>3,'name'=>'Americano','description'=>'Double shot, hot or iced','price'=>110.00,'category_id'=>1,'is_available'=>true,'category'=>(object)['name'=>'Coffee']],
            (object)['id'=>4,'name'=>'Cappuccino','description'=>'Classic with foam art','price'=>125.00,'category_id'=>1,'is_available'=>true,'category'=>(object)['name'=>'Coffee']],
            (object)['id'=>5,'name'=>'Matcha Latte','description'=>'Stone-ground matcha','price'=>140.00,'category_id'=>2,'is_available'=>true,'category'=>(object)['name'=>'Non-Coffee']],
            (object)['id'=>6,'name'=>'Hot Chocolate','description'=>'Rich Belgian cocoa','price'=>120.00,'category_id'=>2,'is_available'=>true,'category'=>(object)['name'=>'Non-Coffee']],
            (object)['id'=>7,'name'=>'Croissant','description'=>'Buttery, flaky','price'=>85.00,'category_id'=>3,'is_available'=>true,'category'=>(object)['name'=>'Pastry']],
            (object)['id'=>8,'name'=>'Blueberry Muffin','description'=>'Loaded with berries','price'=>95.00,'category_id'=>3,'is_available'=>true,'category'=>(object)['name'=>'Pastry']],
        ]);
    }

    public static function featuredProducts(): Collection
    {
        return self::products()->take(4);
    }

    public static function pastries(): Collection
    {
        return self::products()->where('category_id', 3);
    }

    public static function users(): Collection
    {
        return collect([
            (object)['id'=>1,'first_name'=>'Ana','last_name'=>'Reyes','email'=>'ana@issang.test','phone'=>'0917 111 1111','role'=>'admin','is_active'=>true],
            (object)['id'=>2,'first_name'=>'Ben','last_name'=>'Cruz','email'=>'ben@issang.test','phone'=>'0917 222 2222','role'=>'staff','is_active'=>true],
            (object)['id'=>3,'first_name'=>'Cara','last_name'=>'Lim','email'=>'cara@issang.test','phone'=>'0917 333 3333','role'=>'customer','is_active'=>true],
            (object)['id'=>4,'first_name'=>'Dan','last_name'=>'Tan','email'=>'dan@issang.test','phone'=>'0917 444 4444','role'=>'customer','is_active'=>false],
        ]);
    }

    public static function orders(): Collection
    {
        return collect([
            (object)[
                'id'=>1,'status'=>'pending','total_amount'=>290.00,
                'pickup_date'=>now()->addDay()->toDateString(),'pickup_time'=>'10:00',
                'payment_method'=>'cash','payment_status'=>'pending','notes'=>'',
                'created_at'=>now()->subHours(2),
                'customer'=>(object)['first_name'=>'Cara','last_name'=>'Lim','email'=>'cara@issang.test','phone'=>'0917 333 3333'],
                'items'=>collect([
                    (object)['quantity'=>1,'unit_price'=>145.00,'subtotal'=>145.00,'special_instructions'=>'Extra shot','product'=>(object)['name'=>'Issang Macchiato']],
                    (object)['quantity'=>1,'unit_price'=>145.00,'subtotal'=>145.00,'special_instructions'=>'','product'=>(object)['name'=>'Spanish Latte']],
                ]),
            ],
            (object)[
                'id'=>2,'status'=>'preparing','total_amount'=>220.00,
                'pickup_date'=>now()->toDateString(),'pickup_time'=>'14:30',
                'payment_method'=>'gcash','payment_status'=>'pending','notes'=>'',
                'created_at'=>now()->subHours(1),
                'customer'=>(object)['first_name'=>'Guest','last_name'=>'—','email'=>'','phone'=>'0917 555 5555'],
                'items'=>collect([
                    (object)['quantity'=>2,'unit_price'=>110.00,'subtotal'=>220.00,'special_instructions'=>'','product'=>(object)['name'=>'Americano']],
                ]),
            ],
        ]);
    }

    public static function inventory(): Collection
    {
        return collect([
            (object)['id'=>1,'name'=>'Espresso Beans','category'=>'Coffee Bean','quantity'=>12,'unit'=>'kg','min_threshold'=>5],
            (object)['id'=>2,'name'=>'Whole Milk','category'=>'Dairy','quantity'=>3,'unit'=>'L','min_threshold'=>8],
            (object)['id'=>3,'name'=>'Caramel Syrup','category'=>'Syrup','quantity'=>6,'unit'=>'L','min_threshold'=>3],
            (object)['id'=>4,'name'=>'Matcha Powder','category'=>'Powder','quantity'=>2,'unit'=>'kg','min_threshold'=>4],
            (object)['id'=>5,'name'=>'Paper Cups (16oz)','category'=>'Supplies','quantity'=>450,'unit'=>'pcs','min_threshold'=>100],
        ]);
    }

    public static function sales(): Collection
    {
        return collect([
            (object)['id'=>1,'created_at'=>now()->subHours(3),'total_amount'=>290.00,'payment_method'=>'cash','transaction_type'=>'walk-in','customer'=>(object)['first_name'=>'Ana']],
            (object)['id'=>2,'created_at'=>now()->subHours(2),'total_amount'=>435.00,'payment_method'=>'gcash','transaction_type'=>'pre-order','customer'=>(object)['first_name'=>'Ben']],
        ]);
    }

    public static function announcements(): Collection
    {
        return collect([
            (object)['id'=>1,'title'=>'Holiday Hours','content'=>'We close early on Dec 24.','target_audience'=>'all','is_active'=>true,'created_at'=>now()->subDays(2)],
        ]);
    }

    public static function notifications(): Collection
    {
        return collect([
            (object)['id'=>1,'title'=>'Order Confirmed','message'=>'Your pre-order #00002 is being prepared.','is_read'=>false,'created_at'=>now()->subMinutes(20)],
            (object)['id'=>2,'title'=>'Welcome!','message'=>'Thanks for joining Issang Macchiatos.','is_read'=>true,'created_at'=>now()->subDays(1)],
        ]);
    }
}