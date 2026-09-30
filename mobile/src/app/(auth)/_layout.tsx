import { Stack } from 'expo-router';

import { useTheme } from '@/theme/ThemeProvider';

// Signed-out visitors land on the public landing page first.
export const unstable_settings = { initialRouteName: 'bienvenue' };

export default function AuthLayout() {
  const { colors } = useTheme();
  return (
    <Stack screenOptions={{ headerShown: false, contentStyle: { backgroundColor: colors.bg }, animation: 'fade' }}>
      <Stack.Screen name="bienvenue" />
      <Stack.Screen name="connexion" />
      <Stack.Screen name="inscription" />
      <Stack.Screen name="mot-de-passe-oublie" />
    </Stack>
  );
}
