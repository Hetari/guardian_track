<script setup lang="ts">
  import NavFooter from '@/components/NavFooter.vue';
  import NavMain from '@/components/NavMain.vue';
  import NavUser from '@/components/NavUser.vue';
  import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
  import { type NavItem } from '@/types';
  import { Link } from '@inertiajs/vue3';
  import { Armchair, Barcode, Building2, LayoutGrid, NotepadText, Users } from 'lucide-vue-next';
  import AppLogo from './AppLogo.vue';

  const mainNavItems: NavItem[] = [
    {
      title: 'Dashboard',
      href: '/dashboard',
      icon: LayoutGrid,
    },
    {
      title: 'Users',
      href: '/dashboard/users',
      icon: Users,
    },
    {
      title: 'Reports',
      href: '/dashboard/reports',
      icon: NotepadText,
    },
    {
      title: 'Companies',
      href: '/dashboard/companies',
      icon: Building2,
    },
    {
      title: 'Products',
      href: '/dashboard/products',
      icon: Armchair,
    },

    {
      title: 'Serial Numbers',
      href: '/dashboard/serial-numbers',
      icon: Barcode,
    },
  ];

  const footerNavItems: NavItem[] = [];
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="route('dashboard.index')">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <NavMain :items="mainNavItems" />
    </SidebarContent>

    <SidebarFooter>
      <NavFooter :items="footerNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>
  <slot />
</template>
