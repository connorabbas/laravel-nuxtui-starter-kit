declare namespace App {
namespace Data {
export type ErrorToastResponseData = {
status: number,
errorSummary: string,
errorDetail: string,
errorIcon: string,
};
export type UserData = {
id: number,
name: string,
email: string,
emailVerifiedAt: string | null,
createdAt: string,
updatedAt: string,
};
namespace Users {
export type UserIndexQueryData = {
page: number,
perPage: number,
search: string | null,
verified: boolean | null,
createdFrom: string | null,
createdUntil: string | null,
sort: App.Enums.UserSort,
};
}
}
namespace Enums {
export type UserSort = 'newest' | 'oldest' | 'name_asc' | 'name_desc' | 'email_asc' | 'email_desc';
}
}
