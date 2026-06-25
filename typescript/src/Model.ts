export interface ICategory {
    categoryId: string;
    categoryName: string;
    parentId: string | null;
    courses: ICourse[];
    children: ICategory[];
    createdAt: Date;
    updatedAt: Date;
}

export interface ICourse {
    courseId: string;
    title: string;
    shortDescription: string;
    description: string;
    thumbnail: string;
    price: number;
    isPublished: boolean;
    modules: IModule[];
    createdAt: Date;
    updatedAt: Date;
}
export interface IModule {
    moduleId: string;
    moduleName: string;
    order: number;
    lessons: ILesson[];
    createdAt: Date;
    updatedAt: Date;
}
export interface ILesson {
    lessonId: string;
    lessonName: string;
    order: number;
    content: string;
    createdAt: Date;
    updatedAt: Date;
}

