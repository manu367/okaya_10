export class Validation {

    private constructor() {}

    public static email(value: string): boolean {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(value);
    }

    public static phoneNumber(value: number | string): boolean {
        const str = String(value);
        const regex = /^[0-9]+$/;   // only numbers
        return regex.test(str);
    }
    public static address(value: string): boolean {
        if (!value) return false;
        const trimmed = value.trim();
        return trimmed.length >= 5; // minimal sensible length
    }
    public static password(value: string): boolean {
        const regex =
            /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&_\-#])[A-Za-z\d@$!%*?&_\-#]{8,}$/;
        return regex.test(value);
    }
}