import {GoalType} from '@givewp/campaigns/types';

export type Campaign = {
    id?: number;
    pageId?: number;
    pagePermalink?: string & Location;
    type: string;
    title: string;
    shortDescription: string;
    longDescription: string;
    logo: string;
    image: string;
    primaryColor: string;
    secondaryColor: string;
    goalType: GoalType;
    goal: number;
    goalStats: {
        actual: number;
        percentage: number;
        goal: number;
    };
    status: string;
    startDateTime: {
        date: string;
        timezone_type: number;
        timezone: string;
    };
    endDateTime: {
        date: string;
        timezone_type: number;
        timezone: string;
    };
    createdAt: string;
    defaultFormId: number;
    defaultFormTitle: string;
};

export type CampaignEntity = {
    campaign: Campaign;
    hasResolved: boolean;
    edit: (data: Campaign) => void
    save: () => any
}

/*export interface Campaign {
    id: number;
    title: string;
    type: string;
    status: string;
    shortDescription: string;
    longDescription: string;
    logo: string;
    image: string;
    goal: number;
}*/
