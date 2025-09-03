import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useAuth() {
    const page = usePage();
    const roles = computed(() => page.props.roles ?? {});
    const user = computed(() => page.props.auth?.user ?? null);
    const userRole = computed(() => page.props.auth?.user_roles ?? []);

    const hasRole = (role) => {
        return userRole.value?.includes(role) ?? false;
    };

    return { page, roles, user, hasRole };
}
