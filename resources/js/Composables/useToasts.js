import { usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { toast } from 'vue3-toastify';

const page = usePage();
const toasts = ref();

const reToasted = ref(false);

const fireToast = (notification, sticky = false) => {
    toast(notification.message, {
        toastId: notification.id,
        type: notification.type,
        newestOnTop: true,
        theme: toast.THEME.LIGHT,
        autoClose: !sticky && (notification.type == 'success' ? 3000 : false),
        closeOnClick: !sticky,
        onClose: () => (reToasted.value = false),
    });
};

const fireToasts = (sticky = false) => {
    toasts.value?.forEach((notification) => fireToast(notification, sticky));
};

const toastAgain = () => {
    reToasted.value ? toast.remove() : fireToasts(true);
    reToasted.value = !reToasted.value;
};

watch(
    () => page?.props?.flash.toasts,
    (newToasts) => {
        reToasted.value = false;
        toasts.value = newToasts;
        fireToasts();
    }
);

export function useToasts() {
    return {
        toasts,
        toastAgain,
        reToasted,
    };
}
