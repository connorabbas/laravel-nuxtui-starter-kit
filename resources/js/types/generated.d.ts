declare namespace App {
namespace Data {
export type AccountData = {
id: number,
name: string,
provider: string | null,
status: string,
balance: string,
openedAt: string | string | string | null,
};
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
emailVerifiedAt: string | string | null,
createdAt: string | string | string,
updatedAt: string | string | string,
accounts: App.Data.AccountData[] | null,
accountsCount: number | null,
accountsSumBalance: string | null,
accountsMaxOpenedAt: string | string | string | null,
};
}
}
