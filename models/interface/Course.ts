import {NoteType} from "../constants/GlobalConstants";

interface BaseCourse{
    name: string;
    description: string;
    order:number;
    status:number;
    type:NoteType;
    createdBy:string;
    createAt:Date;
    updateAt:Date;
}