import { test, expect } from '@playwright/test';
import {
  loginAndOpenAddForm, openAddForm, openNhanVienList,
  loginAndOpenNhanVienList, loginAndOpenEditForm, removeRequiredAttr,
} from './helpers';
import path from 'path';

const BASE_API = 'http://localhost/QLShopDT_API/api/nhanvien';

const dsNhanVien = [
  { tenNV: 'Lê Quảng An',       sdt: '0901234567', ns: '1995-06-15', diachi: '123 Đường Lê Lợi, TP.HCM', username: 'nvAn' },
  { tenNV: 'Đoàn Mạnh Hùng',   sdt: '0912345678', ns: '1990-03-22', diachi: '45 Nguyễn Huệ, Hà Nội',    username: 'nvHung' },
  { tenNV: 'Trần Nguyễn Hoàng', sdt: '0923456789', ns: '1988-11-05', diachi: '78 Trần Phú, Đà Nẵng',    username: 'nvHoang' },
  { tenNV: 'Lương Thế Anh',    sdt: '0934567890', ns: '1992-07-20', diachi: '56 Lý Thường Kiệt, Huế',   username: 'nvAnh' },
  { tenNV: 'Nguyễn Trọng Tấn', sdt: '0945678901', ns: '1985-09-10', diachi: '12 Đinh Tiên Hoàng, HCM',  username: 'nvTan' },
];

test.describe('Quản lý nhân viên', () => {
    test.use({ storageState: path.join(__dirname, '../../.auth/admin.json') });
    // Xóa nhân viên cũ cùng tên
  test.beforeAll(async ({ browser }) => {
    const ctx = await browser.newContext({
      storageState: path.join(__dirname, '../../.auth/admin.json'),
    });
    const pg  = await ctx.newPage();
    const res  = await pg.request.get(BASE_API);
    const json = await res.json();
    const names = new Set(dsNhanVien.map(nv => nv.tenNV));
    for (const emp of (json.data ?? [])) {
      if (names.has(emp.tennv)) {
        await pg.request.delete(`${BASE_API}/${emp.manv}`);
      }
    }
    await ctx.close();
  });

  // ── Thêm nhân viên ──────────────────────────────────────────────

  test('Thêm nhiều nhân viên hợp lệ thành công', async ({ page }) => {
    for (let i = 0; i < dsNhanVien.length; i++) {
      const nv = dsNhanVien[i];
      if (i === 0) await loginAndOpenAddForm(page);
      else         await openAddForm(page);

      await page.getByRole('textbox', { name: 'Tên nhân viên' }).fill(nv.tenNV);
      await page.getByRole('textbox', { name: 'Địa chỉ' }).fill(nv.diachi);
      await page.getByRole('textbox', { name: 'Số điện thoại' }).fill(nv.sdt);
      await page.locator('#date_ns').fill(nv.ns);
      await page.getByRole('textbox', { name: 'Tên đăng nhập' }).fill(nv.username);
      await page.getByRole('textbox', { name: 'Mật khẩu' }).fill('123456');
      await page.getByRole('button', { name: 'Thêm nhân viên' }).click();

      await expect(page.getByText(`Thêm nhân viên "${nv.tenNV}" thành công`)).toBeVisible();
      await expect(page.getByRole('cell', { name: nv.tenNV, exact: true }).last()).toBeVisible();
    }
  });

  test('Thêm nhân viên bỏ trống tên', async ({ page }) => {
    await loginAndOpenAddForm(page);
    await removeRequiredAttr(page, 'txt_tennv');
    await page.getByRole('textbox', { name: 'Tên đăng nhập' }).fill('nvAn');
    await page.getByRole('textbox', { name: 'Mật khẩu' }).fill('123456');
    await page.getByRole('button', { name: 'Thêm nhân viên' }).click();
    await expect(page.getByText('Tên nhân viên không được để trống')).toBeVisible();
  });

  test('Thêm nhân viên bỏ trống tên đăng nhập', async ({ page }) => {
    await loginAndOpenAddForm(page);
    await page.getByRole('textbox', { name: 'Tên nhân viên' }).fill('Lê Quảng An');
    await removeRequiredAttr(page, 'txt_username');
    await page.getByRole('textbox', { name: 'Mật khẩu' }).fill('123456');
    await page.getByRole('button', { name: 'Thêm nhân viên' }).click();
    await expect(page.getByText('Tên đăng nhập và mật khẩu không được để trống')).toBeVisible();
  });

  test('Thêm nhân viên bỏ trống mật khẩu', async ({ page }) => {
    await loginAndOpenAddForm(page);
    await page.getByRole('textbox', { name: 'Tên nhân viên' }).fill('Đoàn Mạnh Hùng');
    await page.getByRole('textbox', { name: 'Tên đăng nhập' }).fill('nvHung');
    await removeRequiredAttr(page, 'txt_password');
    await page.getByRole('button', { name: 'Thêm nhân viên' }).click();
    await expect(page.getByText('Tên đăng nhập và mật khẩu không được để trống')).toBeVisible();
  });

  test('Thêm nhân viên tên đăng nhập đã tồn tại', async ({ page }) => {
    await loginAndOpenAddForm(page);
    await page.getByRole('textbox', { name: 'Tên nhân viên' }).fill('Trần Nguyễn Hoàng');
    await page.getByRole('textbox', { name: 'Tên đăng nhập' }).fill('admin');
    await page.getByRole('textbox', { name: 'Mật khẩu' }).fill('123456');
    await page.getByRole('button', { name: 'Thêm nhân viên' }).click();
    await expect(page.getByText('Tên đăng nhập đã tồn tại')).toBeVisible();
  });

  // ── Tìm kiếm nhân viên ──────────────────────────────────────────

  test('Tìm kiếm nhân viên có kết quả', async ({ page }) => {
    await loginAndOpenNhanVienList(page);
    const firstName = await page.getByRole('row').nth(1).getByRole('cell').nth(2).textContent();
    const keyword = firstName?.trim().split(' ').pop() ?? 'a';
    await page.getByRole('textbox', { name: /Tìm theo tên/ }).fill(keyword!);
    await page.getByRole('button', { name: 'Tìm' }).click();
    await expect(page.getByText(new RegExp(`Kết quả cho "${keyword}"`))).toBeVisible();
    await expect(page.getByRole('cell', { name: keyword, exact: false }).first()).toBeVisible();
  });

  test('Tìm kiếm nhân viên không có kết quả', async ({ page }) => {
    await loginAndOpenNhanVienList(page);
    await page.getByRole('textbox', { name: /Tìm theo tên/ }).fill('xyz123');
    await page.getByRole('button', { name: 'Tìm' }).click();
    await expect(page.getByText('Không tìm thấy nhân viên nào')).toBeVisible();
  });

  // ── Sửa nhân viên ───────────────────────────────────────────────

  test('Sửa nhân viên hợp lệ thành công', async ({ page }) => {
    await loginAndOpenEditForm(page);
    await page.getByRole('textbox', { name: 'Tên nhân viên' }).fill('Shion The Shaun');
    await page.getByRole('textbox', { name: 'Số điện thoại' }).fill('0987654321');
    await page.locator('#date_ns').fill('1990-01-20');
    await page.getByRole('button', { name: 'Cập nhật' }).click();
    await expect(page.getByText('Cập nhật nhân viên thành công')).toBeVisible();
    await expect(page.getByRole('cell', { name: 'Shion The Shaun', exact: true }).last()).toBeVisible();
  });

  test('Sửa nhân viên bỏ trống tên', async ({ page }) => {
    await loginAndOpenEditForm(page);
    await removeRequiredAttr(page, 'txt_tennv');
    await page.getByRole('textbox', { name: 'Tên nhân viên' }).fill('');
    await page.getByRole('button', { name: 'Cập nhật' }).click();
    await expect(page.getByText('Tên nhân viên không được để trống')).toBeVisible();
  });

  // ── Xóa nhân viên ───────────────────────────────────────────────

  test('Xóa nhân viên thành công', async ({ page }) => {
    await openNhanVienList(page);

    const tenNV = (await page.getByRole('row').nth(1).getByRole('cell').nth(2).textContent())?.trim() ?? '';

    const row = page.getByRole('row', { name: tenNV, exact: false }).first();
    await row.getByRole('link', { name: 'Xóa' }).click();
    await expect(page.getByText(`Bạn có chắc muốn xóa nhân viên "${tenNV}" không?`)).toBeVisible();
    await page.locator('#deleteConfirmBtn').click();
    await expect(page.getByText('Xóa nhân viên thành công')).toBeVisible();
    await expect(page.getByRole('cell', { name: tenNV, exact: true }).first()).not.toBeVisible();
  });

  // ── Validate ────────────────────────────────────────────────────

  test('Thêm nhân viên mật khẩu dưới 6 ký tự', async ({ page }) => {
    await openAddForm(page);
    await page.getByRole('textbox', { name: 'Tên nhân viên' }).fill('Lương Thế Anh');
    await page.getByRole('textbox', { name: 'Tên đăng nhập' }).fill('nvAnh');
    await page.evaluate(() => { document.getElementById('txt_password')?.removeAttribute('minlength'); });
    await page.getByRole('textbox', { name: /Mật khẩu/ }).fill('123');
    await page.getByRole('button', { name: 'Thêm nhân viên' }).click();
    await expect(page.getByText('Mật khẩu phải có ít nhất 6 ký tự')).toBeVisible();
  });

  test('Thêm nhân viên số điện thoại không hợp lệ', async ({ page }) => {
    await openAddForm(page);
    await page.getByRole('textbox', { name: 'Tên nhân viên' }).fill('Nguyễn Trọng Tấn');
    await page.evaluate(() => { document.getElementById('txt_sdt')?.removeAttribute('pattern'); });
    await page.getByRole('textbox', { name: 'Số điện thoại' }).fill('abc123');
    await page.getByRole('textbox', { name: 'Tên đăng nhập' }).fill('nvTan');
    await page.getByRole('textbox', { name: /Mật khẩu/ }).fill('123456');
    await page.getByRole('button', { name: 'Thêm nhân viên' }).click();
    await expect(page.getByText('Số điện thoại không hợp lệ')).toBeVisible();
  });

  test('Thêm nhân viên chưa đủ 18 tuổi', async ({ page }) => {
    await openAddForm(page);
    await page.getByRole('textbox', { name: 'Tên nhân viên' }).fill('Lê Quảng An');
    await page.locator('#date_ns').fill('2015-01-01');
    await page.getByRole('textbox', { name: 'Tên đăng nhập' }).fill('nvAn');
    await page.getByRole('textbox', { name: /Mật khẩu/ }).fill('123456');
    await page.getByRole('button', { name: 'Thêm nhân viên' }).click();
    await expect(page.getByText('Nhân viên phải đủ 18 tuổi')).toBeVisible();
  });

  test('Thêm nhân viên tên vượt quá 100 ký tự', async ({ page }) => {
    await openAddForm(page);
    await page.evaluate(() => { document.getElementById('txt_tennv')?.removeAttribute('maxlength'); });
    await page.getByRole('textbox', { name: 'Tên nhân viên' }).fill('A'.repeat(101));
    await page.getByRole('textbox', { name: 'Tên đăng nhập' }).fill('nvAn');
    await page.getByRole('textbox', { name: /Mật khẩu/ }).fill('123456');
    await page.getByRole('button', { name: 'Thêm nhân viên' }).click();
    await expect(page.getByText('Tên nhân viên không được quá 100 ký tự')).toBeVisible();
  });

});
