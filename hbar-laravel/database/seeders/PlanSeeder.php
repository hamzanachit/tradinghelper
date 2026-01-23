<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Perfect for getting started with HBAR trading',
                'price_monthly' => 0.00,
                'price_yearly' => 0.00,
                'features' => json_encode([
                    'Basic charts',
                    '5 Price alerts',
                    '10 Drawings',
                    'Paper trading ($10k)',
                    'RSI indicator'
                ]),
                'limits' => json_encode([
                    'price_alerts' => 5,
                    'drawings' => 10,
                    'paper_balance' => 10000,
                    'indicators' => ['rsi']
                ]),
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'For casual traders who need more features',
                'price_monthly' => 9.99,
                'price_yearly' => 99.99,
                'features' => json_encode([
                    'Everything in Free',
                    '15 Price alerts',
                    '50 Drawings',
                    'EMA indicator',
                    'Data export'
                ]),
                'limits' => json_encode([
                    'price_alerts' => 15,
                    'drawings' => 50,
                    'paper_balance' => 10000,
                    'indicators' => ['rsi', 'ema']
                ]),
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'For serious traders who need professional tools',
                'price_monthly' => 29.99,
                'price_yearly' => 299.99,
                'features' => json_encode([
                    'Everything in Basic',
                    'Unlimited alerts',
                    'Unlimited drawings',
                    'All indicators',
                    'Backtesting',
                    'Replay mode',
                    'Priority support'
                ]),
                'limits' => json_encode([
                    'price_alerts' => -1,
                    'drawings' => -1,
                    'paper_balance' => 100000,
                    'indicators' => ['rsi', 'ema', 'macd', 'bb', 'sma', 'wma']
                ]),
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'For trading teams and professional traders',
                'price_monthly' => 99.99,
                'price_yearly' => 999.99,
                'features' => json_encode([
                    'Everything in Pro',
                    'Team collaboration',
                    'White-label options',
                    'Custom integrations',
                    'API access',
                    'Dedicated support',
                    'White-label mobile app'
                ]),
                'limits' => json_encode([
                    'price_alerts' => -1,
                    'drawings' => -1,
                    'paper_balance' => -1,
                    'indicators' => ['rsi', 'ema', 'macd', 'bb', 'sma', 'wma', 'ichimoku', 'atr', 'stoch']
                ]),
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('plans')->insert($plans);
    }
}