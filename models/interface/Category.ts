// models.ts

export enum NoteType {
    CATEGORY = "CATEGORY",
    COURSE = "COURSE",
}

export enum UserRole {
    ADMIN = "ADMIN",
    USER = "USER",
}

export enum Operations {
    CREATE = "CREATE",
    READ = "READ",
    UPDATE = "UPDATE",
    DELETE = "DELETE",
    ACCESS = "ACCESS",
}


export interface Category {
    id: string;
    name: string;
    slug?:string;
    description?: string;
    parentCategoryId?: string | null;
    categories?: Category[];
    courses?: Course[];
    createdAt: Date;
    updatedAt: Date;
}

export interface Course {
    id: string;
    title: string;
    description?: string;
    Instructor?:string;
    students?:number;
    price?:number;
    thumnail?:string;
    module?:number;
    // Category to which course belongs
    categoryId: string;
    createdAt: Date;
    updatedAt: Date;
}

export interface Test{
    testname:string;
    jsonQuestion:string;
}
export interface TestSeries{
    id: string;
    title: string;
    description?: string;
    tags:string[];
    status:UserStatus;
    Instructor?:string;
    students?:number;
    price?:number;
    testseries:Test[];
    totoalQuestion:number;
    categoryId: string;
    subcategoryid:string;
    createdAt: Date;
    updatedAt: Date;
}

export interface Question{
    jsonQuestion:string[]
}
export interface QuestionBank{
    id: string;
    title: string;
    description?: string;
    tags:string[];
    status:UserStatus;
    Instructor?:string;
    students?:number;
    price?:number;
    questionBank:Question;

}

export interface PreviousYearPaper{
    id: string;
    josnQuestion:string[];
}
export interface PerviousYearQuestionPaper{
    id: string;
    title: string;
    description?: string;
    tags:string[];
    status:UserStatus;
    Instructor?:string;
    students?:number;
    year:number;
    question:number;
    qustionpaper:PreviousYearPaper[];
}


export interface ImagesUploader{
    id:number;
    name:string;
    link:string;
    source:string;
    createat:Date;
    updateat:Date;
    status:UserStatus;
}
export interface VideoUploader{
    id:number;
    name:string;
    link:string;
    source:string;
    createat:Date;
    updateat:Date;
    status:UserStatus;
}
export interface DocumentUploader{
    id:number;
    name:string;
    link:string;
    source:string;
    createat:Date;
    updateat:Date;
    status:UserStatus;
}
export interface AudioUploader{
    id:number;
    name:string;
    link:string;
    source:string;
    createat:Date;
    updateat:Date;
    status:UserStatus;
}
export interface UserAccess{
    type?:UserRole;
    permission?:Operations[];
}

export enum UserStatus {
    ACTIVE = "ACTIVE",
    INACTIVE = "INACTIVE",
    BLOCKED = "BLOCKED",
    PENDING = "PENDING",
    DRAFT="DRAFT",
    PUBLISHED="PUBLISHED"
}
export interface Session {
    id: string;
    token: string;
    device?: string;
    ipAddress?: string;
    isActive: boolean;
    lastSeenAt: Date;
    createdAt: Date;
}
export interface UserProfile {
    firstName: string;
    lastName?: string;
    avatar?: string;
    phone?: string;
    bio?: string;
}

export interface User {
    id: string;
    profile:UserProfile;
    email: string;
    password: string;
    status:UserStatus;
    session:Session[];
    role: UserAccess;
    courseEnrollerd?:Course[];
    progress?:number;
    setting?:AppSetting;
    createdAt: Date;
    updatedAt: Date;
}
export enum ThemeMode {
    LIGHT = "LIGHT",
    DARK = "DARK",
    SYSTEM = "SYSTEM",
}
export enum LayoutType {
    GRID = "GRID",
    LIST = "LIST",
}
export interface ThemeSettings {
    mode: ThemeMode;
    primaryColor: string;
    secondaryColor: string;
    backgroundColor: string;
    textColor: string;
    cardColor: string;
    cardBorderRadius: number;
}
export interface LayoutSettings {
    type: LayoutType;
    sidebarCollapsed: boolean;
    showBreadcrumbs: boolean;
    showHeader: boolean;
    showFooter: boolean;
}
export interface AppSetting {
    id: string;
    theme: ThemeSettings;
    layout: LayoutSettings;
    language: string;
    notificationsEnabled: boolean;
    createdAt: Date;
    updatedAt: Date;
}

const use:User={
    id:"1",
    profile:{
        firstName:"",
        lastName:"",
        avatar:"",
        phone:"",
        bio:""
    },
    email:"pathak@mm.com",
    password:"Iammanupathak",
    status:UserStatus.ACTIVE,
    session:[
        {
            id: "1",
            token:"scSCSDDSC23324#009@SDDCSDC",
            device:"MOBILE",
            ipAddress:"::1",
            isActive:true,
            lastSeenAt: new Date(),
            createdAt: new Date()
        }
    ],
    role:{
        type:UserRole.ADMIN,
        permission:[Operations.ACCESS,Operations.CREATE,Operations.READ,Operations.UPDATE]
    },
    createdAt:new Date(),
    updatedAt:new Date()
}
export const sampleCategory: Category = {
    id: "cat-1",
    name: "Programming",
    categories: [
        {
            id: "cat-2",
            name: "Java",
            parentCategoryId: "cat-1",
            courses: [
                {
                    id: "course-1",
                    title: "Spring Boot Masterclass",
                    categoryId: "cat-2",
                    createdAt: new Date(),
                    updatedAt: new Date(),
                },
            ],
            createdAt: new Date(),
            updatedAt: new Date(),
        },
    ],
    courses: [
        {
            id: "course-2",
            title: "Node.js Complete Guide",
            categoryId: "cat-1",
            createdAt: new Date(),
            updatedAt: new Date(),
        },
    ],
    createdAt: new Date(),
    updatedAt: new Date(),
};