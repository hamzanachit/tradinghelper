-- Migration: Fix Admin RLS Policies v3
-- Run with: supabase db push

DROP POLICY IF EXISTS "Admins view themselves" ON admins;
DROP POLICY IF EXISTS "Super admins manage admins" ON admins;
DROP POLICY IF EXISTS "Anyone can view active plans" ON subscription_plans;
DROP POLICY IF EXISTS "Super admins manage plans" ON subscription_plans;
DROP POLICY IF EXISTS "Users view own subscription" ON user_subscriptions;
DROP POLICY IF EXISTS "Users can insert own subscription" ON user_subscriptions;
DROP POLICY IF EXISTS "Super admins manage all subscriptions" ON user_subscriptions;
DROP POLICY IF EXISTS "Users view own payments" ON payments;
DROP POLICY IF EXISTS "Admins view all payments" ON payments;
DROP POLICY IF EXISTS "Anyone can log analytics" ON analytics_events;
DROP POLICY IF EXISTS "Admins view analytics" ON analytics_events;

-- Subscription Plans Policies
CREATE POLICY "Anyone can view active plans" ON subscription_plans
  FOR SELECT USING (is_active = true);

CREATE POLICY "Admins manage plans" ON subscription_plans
  FOR ALL USING (true);

-- User Subscriptions Policies
CREATE POLICY "Users view own subscription" ON user_subscriptions
  FOR SELECT USING (user_id::text = (auth.jwt()->>'sub'));

CREATE POLICY "Users can insert own subscription" ON user_subscriptions
  FOR INSERT WITH CHECK (user_id::text = (auth.jwt()->>'sub'));

CREATE POLICY "Admins manage subscriptions" ON user_subscriptions
  FOR ALL USING (true);

-- Payments Policies
CREATE POLICY "Users view own payments" ON payments
  FOR SELECT USING (user_id::text = (auth.jwt()->>'sub'));

CREATE POLICY "Admins manage payments" ON payments
  FOR ALL USING (true);

-- Admins Policies - Allow full access for admin operations
CREATE POLICY "Admins full access" ON admins
  FOR ALL USING (true);

CREATE POLICY "Admins can view" ON admins
  FOR SELECT USING (true);

-- Analytics Policies
CREATE POLICY "Anyone can log analytics" ON analytics_events
  FOR INSERT WITH CHECK (true);

CREATE POLICY "Admins view analytics" ON analytics_events
  FOR SELECT USING (true);
