import { useToast } from "vue-toastification";

export function useNotifications() {
    const toast = useToast();

    // The message is passed in from the caller (like your Layout watcher)
    const notifySuccess = (message) => {
        toast.success(message || "Operation Successful!");
    };

    const notifyError = (message) => {
        toast.error(message || "An error occurred.");
    };

    return { notifySuccess, notifyError };
}