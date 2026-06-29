export class RegisterPage {
  constructor(page){
    this.page = page;
    this.errorMessage    = 
    this.username        = this.page.getByRole('textbox', { name: 'Nhập tên đăng nhập' });
    this.password        = this.page.getByRole('textbox', { name: 'Mật khẩu', exact: true });
    this.confirmPassword = this.page.getByRole('textbox', { name: 'Nhập lại mật khẩu' });
    this.fullname        = this.page.getByRole('textbox', { name: 'Nhập họ và tên đầy đủ' });
  }

  async goto() {
    await this.page.goto('http://localhost/QLShopDT_API/views/auth/register.php');
  }

  async fill(username, password, confirmPassword, fullname) {
    await this.username.fill(username);
    await this.password.fill(password);
    await this.confirmPassword.fill(confirmPassword);
    await this.fullname.fill(fullname);
  }

  async confirm(){
    await this.page.getByRole('button', { name: ' Đăng ký' }).click();
  }
}