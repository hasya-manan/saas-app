import { watch } from 'vue';

export function useIcParser(form) {
    const startWatchingIc = () => {
        watch(() => form.ic_number, (newIc) => {
            if (!newIc) return;

            // Remove non-numeric characters
            const cleanIc = newIc.replace(/\D/g, '');

            // Proceed once we have the first 6 digits (YYMMDD)
            if (cleanIc.length >= 6) {
                const year = cleanIc.substring(0, 2);
                const month = cleanIc.substring(2, 4);
                const day = cleanIc.substring(4, 6);

                /**
                 * Determine Century (19xx vs 20xx)
                 * Current Year is 2026, so yearThreshold is 26.
                 * * Logic for HR System:
                 * 1. If IC year is > 26 (e.g., 70, 80, 99), they were born in 19xx.
                 * 2. If IC year is <= 26 (e.g., 00, 10, 25), they were born in 20xx.
                 * * Assumption: Employees are under 100 years old. 100 and above do not working anymore
                 */

                const yearThreshold = new Date().getFullYear() % 100;
                const fullYear = parseInt(year) > yearThreshold ? `19${year}` : `20${year}`;

                // Ensure the month and day are valid before updating the form
                if (parseInt(month) <= 12 && parseInt(day) <= 31) {
                    form.dob = `${fullYear}-${month}-${day}`;
                }
            }
        });
    };

    return { startWatchingIc };
}