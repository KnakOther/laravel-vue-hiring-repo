import { Recipient } from "./Recipient";

export type Campaign = {
    id: number;
    email_name: string;
    knak_email_id: string;
    send_date: string;
    openRate: string;
    clickRate: string;
    sent: boolean;
    stats: {
        total_recipients: number;
        opened_percentage: number;
        clicked_percentage: number;
        delivered_percentage: number;
    };
    recipients: Recipient[];
};

