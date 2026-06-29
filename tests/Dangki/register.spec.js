import { test, expect } from '@playwright/test';
import { RegisterPage } from './helpers/RegisterPage.js';
import { deleteTempAcc } from './helpers/deleteTempAcc.js';

test.describe('Đăng ký', () => {
  test('Báo lỗi khi không điền đủ thông tin', async ({ page }) => {
    let registerPage; registerPage = new RegisterPage(page);

    await registerPage.goto();
    await registerPage.fill("testAcc", "Alcenium@2005", "Alcenium@2005", "");
    await registerPage.confirm();

    await expect(page).toHaveTitle(/Đăng ký/);
    await expect(page.locator('body')).toContainText('Các trường yêu cầu(*) chưa được điền đầy đủ');
  });

  test('Báo lỗi khi mật khẩu dưới 6 ký tự', async ({ page }) => {
    let registerPage; registerPage = new RegisterPage(page);

    await registerPage.goto();
    await registerPage.fill("testAcc", "1234", "Alcenium@2005", "Đoàn Mạnh Hùng");
    await registerPage.confirm();

    await expect(page).toHaveTitle(/Đăng ký/);
    await expect(page.locator('body')).toContainText('Mật khẩu phải có ít nhất 6 ký tự');
  });

  test('Báo lỗi khi mật khẩu thiếu chữ hoa', async ({ page }) => {
    let registerPage; registerPage = new RegisterPage(page);

    await registerPage.goto();
    await registerPage.fill("testAcc", "alcenium", "alcenium", "Đoàn Mạnh Hùng");
    await registerPage.confirm();

    await expect(page).toHaveTitle(/Đăng ký/);
    await expect(page.locator('body')).toContainText('Mật khẩu phải bao gồm chữ hoa');
  });

  test('Báo lỗi khi mật khẩu thiếu số', async ({ page }) => {
    let registerPage; registerPage = new RegisterPage(page);

    await registerPage.goto();
    await registerPage.fill("testAcc", "Alcenium", "Alcenium", "Đoàn Mạnh Hùng");
    await registerPage.confirm();

    await expect(page).toHaveTitle(/Đăng ký/);
    await expect(page.locator('body')).toContainText('Mật khẩu phải chứa ít nhất 1 số');
  });

  test('Báo lỗi khi mật khẩu thiếu ký tự đặc biệt', async ({ page }) => {
    let registerPage; registerPage = new RegisterPage(page);

    await registerPage.goto();
    await registerPage.fill("testAcc", "Alcenium2005", "Alcenium2005", "Đoàn Mạnh Hùng");
    await registerPage.confirm();

    await expect(page).toHaveTitle(/Đăng ký/);
    await expect(page.locator('body')).toContainText('Mật khẩu phải chứa ít nhất 1 ký tự đặc biệt');
  });

  test('Báo lỗi khi xác nhận mật khẩu không khớp', async ({ page }) => {
    let registerPage; registerPage = new RegisterPage(page);

    await registerPage.goto();
    await registerPage.fill("testAcc", "Alcenium@2005", "a", "Đoàn Mạnh Hùng");
    await registerPage.confirm();

    await expect(page).toHaveTitle(/Đăng ký/);
    await expect(page.locator('body')).toContainText('Mật khẩu xác nhận không khớp');
  });

  test('Báo lỗi khi tên đăng nhập đã tồn tại', async ({ page }) => {
    let registerPage; registerPage = new RegisterPage(page);

    await registerPage.goto();
    await registerPage.fill("admin", "Alcenium@2005", "Alcenium@2005", "Đoàn Mạnh Hùng");
    await registerPage.confirm();

    await expect(page).toHaveTitle(/Đăng ký/);
    await expect(page.locator('body')).toContainText('Tên đăng nhập đã tồn tại');
  });

  test('Đăng nhập thành công', async ({ page }) => {
    let registerPage; registerPage = new RegisterPage(page);

    await registerPage.goto();
    await registerPage.fill("testAcc", "Alcenium@2005", "Alcenium@2005", "Đoàn Mạnh Hùng");
    await registerPage.confirm();

    await expect(page).toHaveTitle(/Đăng nhập/);
    await deleteTempAcc();
  });
});