export async function deleteTempAcc() {
  const mariadb = require('mariadb');
  const conn = await mariadb.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'qlshopdienthoai'
  });

  try {
    await conn.query('delete from taikhoan where tentk="testAcc"');
  } finally {
    conn.end();
  }
}