import { router } from '@inertiajs/vue3';

// We pass the 'form' into this function so it knows which form to update
export function useEmailValidation(form) {
    
    const checkEmailUnique = () => {
        // 1. Only check if there is an '@' to avoid unnecessary server hits
        if (!form.email || !form.email.includes('@')) return;

        // 2. Send the request to your Laravel backend
        router.post(route('validation.email'), {
            email: form.email
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                // If the backend says it's okay, clear the error
                form.clearErrors('email');
            },
            onError: (errors) => {
                // If the backend finds a duplicate, set the error in the form
                form.setError('email', errors.email);
            }
        });
    };

    // Return the function so the page can use it
    return { checkEmailUnique };
}