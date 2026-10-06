declare namespace App {
namespace Data {
export type ErrorToastResponseData = {
status: number,
errorSummary: string,
errorDetail: string,
errorIcon: string,
};
export type FilterOptionData = {
value: string | number | boolean | null,
label: string,
};
export type PaginatedData = {
page: number,
perPage: number,
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
userIds: string[] | null,
verified: boolean | null,
verifiedAt: string | null,
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
declare namespace Illuminate {
export type CursorPaginator<TKey, TValue> = {
data: TKey extends string ? Record<TKey, TValue> : TValue[],
links: {
url: string | null,
label: string,
active: boolean,
}[],
meta: {
path: string,
per_page: number,
next_cursor: string | null,
next_page_url: string | null,
prev_cursor: string | null,
prev_page_url: string | null,
},
};
export type CursorPaginatorInterface<TKey, TValue> = Illuminate.CursorPaginator<TKey, TValue>;
export type LengthAwarePaginator<TKey, TValue> = {
data: TKey extends string ? Record<TKey, TValue> : TValue[],
links: {
url: string | null,
label: string,
active: boolean,
}[],
meta: {
total: number,
current_page: number,
first_page_url: string,
from: number | null,
last_page: number,
last_page_url: string,
next_page_url: string | null,
path: string,
per_page: number,
prev_page_url: string | null,
to: number | null,
},
};
export type LengthAwarePaginatorInterface<TKey, TValue> = Illuminate.LengthAwarePaginator<TKey, TValue>;
}
declare namespace Spatie {
namespace LaravelData {
export type CursorPaginatedDataCollection<TKey, TValue> = Illuminate.CursorPaginator<TKey, TValue>;
export type PaginatedDataCollection<TKey, TValue> = Illuminate.LengthAwarePaginator<TKey, TValue>;
}
}
