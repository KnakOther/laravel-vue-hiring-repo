export type Recipient = {
    id: string;
    first_name: string;
    last_name: string;
    email: string;
    department: string;
    position: string;
    location: string;
    avatar_url: string;
    pivot: {
        sent_at?: datetime;
        delivered_at?: datetime;
        opened_at?: datetime;
        clicked_at?: datetime;
        failed_at?: datetime;
        html: string;
    }
}
