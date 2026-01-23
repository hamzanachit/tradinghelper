-- Migration: SaaS Subscription System
-- Run with: supabase db push

-- Subscription Plans Table
CREATE TABLE IF NOT EXISTS subscription_plans (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  name TEXT NOT NULL,
  slug TEXT NOT NULL UNIQUE,
  description TEXT,
  price_monthly NUMERIC NOT NULL,
  price_yearly NUMERIC NOT NULL,
  features JSONB DEFAULT '[]'::jsonb,
  limits JSONB DEFAULT '{
    "max_alerts": 5,
    "max_drawings": 10,
    "backtesting": false,
    "replay_mode": false,
    "export_data": false,
    "priority_support": false
  }'::jsonb,
  is_active BOOLEAN DEFAULT TRUE,
  sort_order INTEGER DEFAULT 0,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- User Subscriptions Table
CREATE TABLE IF NOT EXISTS user_subscriptions (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID NOT NULL REFERENCES profiles(id) ON DELETE CASCADE,
  plan_id UUID NOT NULL REFERENCES subscription_plans(id),
  status TEXT NOT NULL DEFAULT 'active' CHECK (status IN ('active', 'cancelled', 'expired', 'past_due')),
  billing_cycle TEXT NOT NULL DEFAULT 'monthly' CHECK (billing_cycle IN ('monthly', 'yearly')),
  current_period_start TIMESTAMP WITH TIME ZONE,
  current_period_end TIMESTAMP WITH TIME ZONE,
  cancelled_at TIMESTAMP WITH TIME ZONE,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID NOT NULL REFERENCES profiles(id) ON DELETE CASCADE,
  subscription_id UUID REFERENCES user_subscriptions(id),
  amount NUMERIC NOT NULL,
  currency TEXT DEFAULT 'USD',
  status TEXT NOT NULL DEFAULT 'pending' CHECK (status IN ('pending', 'completed', 'failed', 'refunded')),
  payment_method TEXT,
  transaction_id TEXT,
  metadata JSONB DEFAULT '{}'::jsonb,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  email TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  name TEXT,
  role TEXT NOT NULL DEFAULT 'admin' CHECK (role IN ('super_admin', 'admin', 'support')),
  permissions JSONB DEFAULT '[]'::jsonb,
  is_active BOOLEAN DEFAULT TRUE,
  last_login TIMESTAMP WITH TIME ZONE,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
  updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Analytics Events Table
CREATE TABLE IF NOT EXISTS analytics_events (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  event_type TEXT NOT NULL,
  user_id UUID REFERENCES profiles(id),
  metadata JSONB DEFAULT '{}'::jsonb,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create Indexes
CREATE INDEX IF NOT EXISTS idx_user_subs_user ON user_subscriptions(user_id);
CREATE INDEX IF NOT EXISTS idx_user_subs_plan ON user_subscriptions(plan_id);
CREATE INDEX IF NOT EXISTS idx_user_subs_status ON user_subscriptions(status);
CREATE INDEX IF NOT EXISTS idx_payments_user ON payments(user_id);
CREATE INDEX IF NOT EXISTS idx_payments_status ON payments(status);
CREATE INDEX IF NOT EXISTS idx_analytics_type ON analytics_events(event_type);
CREATE INDEX IF NOT EXISTS idx_analytics_user ON analytics_events(user_id);

-- Enable RLS
ALTER TABLE subscription_plans ENABLE ROW LEVEL SECURITY;
ALTER TABLE user_subscriptions ENABLE ROW LEVEL SECURITY;
ALTER TABLE payments ENABLE ROW LEVEL SECURITY;
ALTER TABLE admins ENABLE ROW LEVEL SECURITY;
ALTER TABLE analytics_events ENABLE ROW LEVEL SECURITY;

-- Drop existing policies
DROP POLICY IF EXISTS "Anyone can view plans" ON subscription_plans;
DROP POLICY IF EXISTS "Admins manage plans" ON subscription_plans;
DROP POLICY IF EXISTS "Users view own subscription" ON user_subscriptions;
DROP POLICY IF EXISTS "Admins manage subscriptions" ON user_subscriptions;
DROP POLICY IF EXISTS "Users view own payments" ON payments;
DROP POLICY IF EXISTS "Admins manage payments" ON payments;
DROP POLICY IF EXISTS "Admins manage admins" ON admins;
DROP POLICY IF EXISTS "Anyone can log analytics" ON analytics_events;
DROP POLICY IF EXISTS "Admins view analytics" ON analytics_events;

-- Subscription Plans Policies
CREATE POLICY "Anyone can view active plans" ON subscription_plans
  FOR SELECT USING (is_active = true);

CREATE POLICY "Super admins manage plans" ON subscription_plans
  FOR ALL USING (
    EXISTS (SELECT 1 FROM admins WHERE id = auth.uid() AND role = 'super_admin')
  );

-- User Subscriptions Policies
CREATE POLICY "Users view own subscription" ON user_subscriptions
  FOR SELECT USING (user_id = auth.uid()::uuid);

CREATE POLICY "Users can insert own subscription" ON user_subscriptions
  FOR INSERT WITH CHECK (user_id = auth.uid()::uuid);

CREATE POLICY "Super admins manage all subscriptions" ON user_subscriptions
  FOR ALL USING (
    EXISTS (SELECT 1 FROM admins WHERE id = auth.uid() AND role = 'super_admin')
  );

-- Payments Policies
CREATE POLICY "Users view own payments" ON payments
  FOR SELECT USING (user_id = auth.uid()::uuid);

CREATE POLICY "Admins view all payments" ON payments
  FOR SELECT USING (
    EXISTS (SELECT 1 FROM admins WHERE id = auth.uid())
  );

-- Admins Policies
CREATE POLICY "Admins can view themselves" ON admins
  FOR SELECT USING (id = auth.uid() OR EXISTS (SELECT 1 FROM admins WHERE id = auth.uid() AND role = 'super_admin'));

CREATE POLICY "Super admins manage admins" ON admins
  FOR ALL USING (
    EXISTS (SELECT 1 FROM admins WHERE id = auth.uid() AND role = 'super_admin')
  );

-- Analytics Policies
CREATE POLICY "Anyone can log analytics" ON analytics_events
  FOR INSERT WITH CHECK (true);

CREATE POLICY "Admins view analytics" ON analytics_events
  FOR SELECT USING (
    EXISTS (SELECT 1 FROM admins WHERE id = auth.uid())
  );

-- Seed Default Plans
INSERT INTO subscription_plans (name, slug, description, price_monthly, price_yearly, features, limits, sort_order) VALUES
('Free', 'free', 'Get started with basic features', 0, 0,
  '["Basic charts", "5 alerts", "10 drawings", "Paper trading"]'::jsonb,
  '{"max_alerts": 5, "max_drawings": 10, "backtesting": false, "replay_mode": false, "export_data": false, "priority_support": false}'::jsonb,
  0),
('Basic', 'basic', 'For active traders', 9.99, 99.99,
  '["Everything in Free", "20 alerts", "50 drawings", "Backtesting", "Basic support"]'::jsonb,
  '{"max_alerts": 20, "max_drawings": 50, "backtesting": true, "replay_mode": false, "export_data": false, "priority_support": false}'::jsonb,
  1),
('Pro', 'pro', 'For professional traders', 29.99, 299.99,
  '["Everything in Basic", "Unlimited alerts", "Unlimited drawings", "Replay mode", "Data export", "API access"]'::jsonb,
  '{"max_alerts": -1, "max_drawings": -1, "backtesting": true, "replay_mode": true, "export_data": true, "priority_support": true}'::jsonb,
  2),
('Premium', 'premium', 'For trading teams', 99.99, 999.99,
  '["Everything in Pro", "Team collaboration", "White-label", "Dedicated support", "Custom integrations"]'::jsonb,
  '{"max_alerts": -1, "max_drawings": -1, "backtesting": true, "replay_mode": true, "export_data": true, "priority_support": true}'::jsonb,
  3)
ON CONFLICT (slug) DO NOTHING;

-- Function to update timestamp
CREATE OR REPLACE FUNCTION update_updated_at()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = NOW();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Add triggers for updated_at
CREATE TRIGGER update_subscription_plans_updated
  BEFORE UPDATE ON subscription_plans
  FOR EACH ROW EXECUTE FUNCTION update_updated_at();

CREATE TRIGGER update_user_subscriptions_updated
  BEFORE UPDATE ON user_subscriptions
  FOR EACH ROW EXECUTE FUNCTION update_updated_at();

CREATE TRIGGER update_admins_updated
  BEFORE UPDATE ON admins
  FOR EACH ROW EXECUTE FUNCTION update_updated_at();

-- Create default admin user (password: admin123 - change this!)
INSERT INTO admins (email, password_hash, name, role)
VALUES ('admin@hbartrading.com', '$2a$10$rQnM1.2CpF9M6R6w8zZqXu6bXk8zZqXu6bXk8zZqXu6bXk8zZqXu6bX', 'Super Admin', 'super_admin')
ON CONFLICT (email) DO NOTHING;
