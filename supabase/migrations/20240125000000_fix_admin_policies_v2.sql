-- Migration: Completely replace admin policies
-- Run with: supabase db push

DROP POLICY IF EXISTS "Anyone can view active plans" ON subscription_plans;
DROP POLICY IF EXISTS "Super admins manage plans" ON subscription_plans;
DROP POLICY IF EXISTS "Users view own subscription" ON user_subscriptions;
DROP POLICY IF EXISTS "Users can insert own subscription" ON user_subscriptions;
DROP POLICY IF EXISTS "Super admins manage all subscriptions" ON user_subscriptions;
DROP POLICY IF EXISTS "Users view own payments" ON payments;
DROP POLICY IF EXISTS "Admins view all payments" ON payments;
DROP POLICY IF EXISTS "Admins can view themselves" ON admins;
DROP POLICY IF EXISTS "Super admins manage admins" ON admins;
DROP POLICY IF EXISTS "Anyone can log analytics" ON analytics_events;
DROP POLICY IF EXISTS "Admins view analytics" ON analytics_events;

-- Subscription Plans Policies
CREATE POLICY "Anyone can view active plans" ON subscription_plans
  FOR SELECT USING (is_active = true);

CREATE POLICY "Super admins manage plans" ON subscription_plans
  FOR ALL USING (
    EXISTS (SELECT 1 FROM admins WHERE email = auth.jwt()->>'email' AND role = 'super_admin')
  );

-- User Subscriptions Policies
CREATE POLICY "Users view own subscription" ON user_subscriptions
  FOR SELECT USING (user_id::text = (auth.jwt()->>'sub') OR user_id::text IN (SELECT id::text FROM profiles WHERE email = auth.jwt()->>'email'));

CREATE POLICY "Users can insert own subscription" ON user_subscriptions
  FOR INSERT WITH CHECK (user_id::text IN (SELECT id::text FROM profiles WHERE email = auth.jwt()->>'email'));

CREATE POLICY "Super admins manage all subscriptions" ON user_subscriptions
  FOR ALL USING (
    EXISTS (SELECT 1 FROM admins WHERE email = auth.jwt()->>'email' AND role = 'super_admin')
  );

-- Payments Policies
CREATE POLICY "Users view own payments" ON payments
  FOR SELECT USING (user_id::text IN (SELECT id::text FROM profiles WHERE email = auth.jwt()->>'email'));

CREATE POLICY "Admins view all payments" ON payments
  FOR SELECT USING (
    EXISTS (SELECT 1 FROM admins WHERE email = auth.jwt()->>'email')
  );

-- Admins Policies
CREATE POLICY "Admins view themselves" ON admins
  FOR SELECT USING (email = auth.jwt()->>'email');

CREATE POLICY "Super admins manage admins" ON admins
  FOR ALL USING (
    EXISTS (SELECT 1 FROM admins WHERE email = auth.jwt()->>'email' AND role = 'super_admin')
  );

-- Analytics Policies
CREATE POLICY "Anyone can log analytics" ON analytics_events
  FOR INSERT WITH CHECK (true);

CREATE POLICY "Admins view analytics" ON analytics_events
  FOR SELECT USING (
    EXISTS (SELECT 1 FROM admins WHERE email = auth.jwt()->>'email')
  );
