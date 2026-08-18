#!/usr/bin/env python3
import asyncio, json, logging, os, sys, time, tempfile, traceback
sys.stdout.reconfigure(encoding='utf-8')
sys.stderr.reconfigure(encoding='utf-8')
from datetime import datetime
from pathlib import Path
from urllib.parse import urlparse
from PIL import Image
from playwright.async_api import async_playwright, ConsoleMessage

BASE_URL = "http://localhost:8080"
LOG_FILE = "bugs_report.log"
ADMIN_EMAIL = "A@gmail.com"
ADMIN_PASSWORDS = ["admin123", "Admin@123", "password", "admin", "P@ssw0rd", "Test@123", "12345678", "Admin123!"]
SIGNUP_EMAIL = f"hunter_{int(time.time())}@test.com"
SIGNUP_USER = "BugHunter"
SIGNUP_PASS = "TestPass@123"
PROD_TITLE = f"BugHunt Prod {int(time.time())}"
PROD_PRICE = "99.99"
PROD_CAT = "هواتف"
PROD_DESC = "Created by Bug Hunting Machine."

SQLI_XSS = [
    ("sqli_basic", "' OR 1=1 --"),
    ("sqli_comment", "' OR '1'='1' -- "),
    ("sqli_union", "' UNION SELECT 1,2,3,4,5 --"),
    ("sqli_admin", "admin' --"),
    ("xss_script", "<script>alert('xss')</script>"),
    ("xss_img", "<img src=x onerror=alert(1)>"),
    ("xss_body", "\"><body onload=alert(1)>"),
    ("sqli_like", "' OR 1=1 LIKE 1 --"),
    ("xss_svg", "<svg onload=alert(1)>"),
]

SIGNUP_FUZZ = [
    ("inv_email_1", {"email":"notanemail"},"Invalid email accepted"),
    ("inv_email_2", {"email":"user@"},"Malformed email accepted"),
    ("inv_email_3", {"email":"@domain.com"},"Email no local part accepted"),
    ("mismatch_pass", {"confirm_password":"DiffPass1!"},"Password mismatch undetected"),
    ("no_accept", {"accept":""},"Terms not enforced"),
    ("empty_email", {"email":""},"Empty email accepted"),
    ("empty_pass", {"password":"","confirm_password":""},"Empty password accepted"),
    ("short_pass", {"password":"ab","confirm_password":"ab"},"Short password accepted"),
    ("xss_name", {"username":"<script>alert(1)</script>"},"XSS in username accepted"),
    ("sqli_name", {"username":"' OR 1=1 --"},"SQLi in username accepted"),
]

def dummy_image(path):
    Image.new("RGB",(200,200),color=(0,120,255)).save(path,"PNG")

class BugLogger:
    def __init__(self):
        self.path = Path(LOG_FILE)
        self.path.write_text("",encoding="utf-8")
        self._log("sys","Bug Hunting Machine started")

    def _log(self,level,msg):
        ts=datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        with self.path.open("a",encoding="utf-8") as f:
            f.write(f"[{ts}] [{level}] {msg}\n")
        print(f"[{ts}] [{level}] {msg}")

    def info(self,m): self._log("INFO",m)
    def warn(self,m): self._log("WARN",m)
    def error(self,m): self._log("ERROR",m)
    def bug(self,m): self._log("BUG",m)
    def ok(self,m): self._log("PASS",m)

class Machine:
    def __init__(self):
        self.log=BugLogger()
        self.play=None; self.browser=None; self.ctx=None; self.page=None
        self.bugs=0; self.admin_pw=None

    async def _on_console(self,msg:ConsoleMessage):
        if msg.type=="error":
            self.log.bug(f"Console error: {msg.text}")
            self.bugs+=1

    async def _on_pageerror(self,err):
        self.log.bug(f"Page crash: {err}")
        self.bugs+=1

    async def _on_reqfail(self,req):
        u=req.url
        if "localhost" not in u or u.startswith("data:"): return
        f=req.failure
        if f:
            err = f.error_text if hasattr(f,'error_text') else str(f)
            self.log.bug(f"Req failed: {u} -> {err}")
            self.bugs+=1

    async def _on_response(self,resp):
        u=resp.url; s=resp.status
        if s>=400 and "localhost" in u:
            self.log.bug(f"HTTP {s}: {u}")

    async def setup(self):
        self.play=await async_playwright().start()
        self.browser=await self.play.chromium.launch(headless=True)
        self.ctx=await self.browser.new_context(viewport={"width":1280,"height":800},locale="ar-EG")
        self.page=await self.ctx.new_page()
        self.page.on("console",self._on_console)
        self.page.on("pageerror",self._on_pageerror)
        self.page.on("requestfailed",self._on_reqfail)
        self.page.on("response",self._on_response)
        self.log.info("Listeners attached")

    async def close(self):
        if self.ctx: await self.ctx.close()
        if self.browser: await self.browser.close()
        if self.play: await self.play.stop()
        self.log.info(f"Shutdown. Bugs: {self.bugs}")

    async def goto(self,path):
        await self.page.goto(f"{BASE_URL}{path}",wait_until="networkidle",timeout=15000)

    async def csrf(self):
        return await self.page.input_value('input[name="csrf_token"]')

    async def snap(self,name):
        await self.page.screenshot(path=f"shot_{name}_{int(time.time())}.png",full_page=True)

    def url_path(self):
        return urlparse(self.page.url).path.rstrip("/") or "/"

    def is_on(self,path):
        return self.url_path() == (path.rstrip("/") or "/")

    async def click_submit(self):
        async with self.page.expect_navigation(timeout=10000) as nav:
            await self.page.click('input[type="submit"]')
        await nav.value

    async def js_submit(self):
        async with self.page.expect_navigation(timeout=10000) as nav:
            await self.page.evaluate("document.querySelector('form').submit()")
        await nav.value

    async def fuzz_login(self):
        self.log.info("=== FUZZ LOGIN ===")
        for n,pay in SQLI_XSS:
            try:
                await self.goto("/login")
                await self.csrf()
                await self.page.fill('input[name="email"]',pay)
                await self.page.fill('input[name="password"]',"x")
                await self.js_submit()
                b=await self.page.text_content("body") or ""
                if "500" in b and ("SQL" in b or "syntax" in b.lower() or "unexpected" in b.lower()):
                    self.log.bug(f"SQLI_XSS [{n}]: server error! payload={pay[:40]}")
                    self.bugs+=1; await self.snap(f"fuzz_{n}")
                elif self.is_on("/"):
                    self.log.bug(f"SQLI_XSS [{n}]: BYPASS! payload={pay[:40]}")
                    self.bugs+=1; await self.snap(f"bypass_{n}")
                elif self.is_on("/login"):
                    if "error" not in self.page.url:
                        self.log.bug(f"SQLI_XSS [{n}]: no error after JS submit. payload={pay[:40]}")
                        self.bugs+=1; await self.snap(f"sus_{n}")
                    else:
                        self.log.ok(f"SQLI_XSS [{n}]: rejected with error")
                else:
                    self.log.ok(f"SQLI_XSS [{n}]: redirected {self.url_path()}")
            except Exception as e:
                self.log.error(f"SQLI_XSS [{n}]: {e}")
        self.log.info("=== FUZZ LOGIN END ===")

    async def fuzz_signup(self):
        self.log.info("=== FUZZ SIGNUP ===")
        ts=int(time.time()); db_bug_reported=False
        for idx,(n,overrides,desc) in enumerate(SIGNUP_FUZZ):
            try:
                unique_email = f"fuzz_{ts}_{idx}@test.com"
                await self.goto("/signup")
                await self.csrf()
                data={"username":SIGNUP_USER,"email":unique_email,"password":SIGNUP_PASS,"confirm_password":SIGNUP_PASS,"accept":"yes"}
                data.update(overrides)
                await self.page.fill('input[name="username"]',data["username"])
                await self.page.fill('input[name="email"]',data["email"])
                await self.page.fill('input[name="password"]',data["password"])
                await self.page.fill('input[name="confirm_password"]',data["confirm_password"])
                cb=self.page.locator('input[name="accept"]')
                if data.get("accept") and await cb.is_visible():
                    await cb.check()
                await self.js_submit()
                if self.is_on("/login"):
                    self.log.warn(f"SIGNUP [{n}]: {desc}")
                    self.bugs+=1; await self.snap(f"signup_{n}")
                else:
                    bt=await self.page.text_content("body") or ""
                    if "full_name" in bt and not db_bug_reported:
                        self.log.bug("DB BUG: User::create() missing 'full_name' column in INSERT -> all signups fail with PDOException")
                        db_bug_reported=True; self.bugs+=1
                    err_hint=""
                    for kw in ["الرجاء","غير صحيحة","غير متطابقة","مسجل","خطأ"]:
                        if kw in bt: err_hint=kw; break
                    self.log.ok(f"SIGNUP [{n}]: rejected (hint:{err_hint})")
            except Exception as e:
                self.log.error(f"SIGNUP [{n}]: {e}")
        if not db_bug_reported:
            self.log.ok("DB BUG not triggered - signup might work now")
        self.log.info("=== FUZZ SIGNUP END ===")

    async def try_admin(self):
        self.log.info("=== ADMIN LOGIN ===")
        for pw in ADMIN_PASSWORDS:
            try:
                await self.goto("/login")
                await self.csrf()
                await self.page.fill('input[name="email"]',ADMIN_EMAIL)
                await self.page.fill('input[name="password"]',pw)
                await self.click_submit()
                if self.is_on("/"):
                    await self.goto("/admin")
                    b=await self.page.text_content("body") or ""
                    if "لوحة الإدارة" in b or "مرحباً" in b:
                        self.log.ok(f"Admin login SUCCESS: {pw}")
                        self.admin_pw=pw
                        return True
                    else:
                        self.log.warn(f"Login OK but not admin: {pw}")
                        return False
                elif "error" in self.page.url:
                    self.log.info(f"Admin try '{pw}': fail")
                else:
                    self.log.warn(f"Admin try '{pw}': unexpected {self.url_path()}")
            except Exception as e:
                self.log.error(f"Admin try '{pw}': {e}")
        self.log.warn("No admin password from wordlist worked.")
        return False

    async def signup_user(self):
        self.log.info("=== SIGNUP USER ===")
        try:
            await self.goto("/signup")
            await self.csrf()
            await self.page.fill('input[name="username"]',SIGNUP_USER)
            await self.page.fill('input[name="email"]',SIGNUP_EMAIL)
            await self.page.fill('input[name="password"]',SIGNUP_PASS)
            await self.page.fill('input[name="confirm_password"]',SIGNUP_PASS)
            cb=self.page.locator('input[name="accept"]')
            if await cb.is_visible():
                await cb.check()
            await self.click_submit()
            if self.is_on("/login"):
                self.log.ok(f"User registered: {SIGNUP_EMAIL}/{SIGNUP_PASS}")
                return True
            bt=await self.page.text_content("body") or ""
            if "full_name" in bt:
                self.log.bug("DB BUG confirmed: User::create() missing 'full_name' in INSERT -> all registrations fail")
                self.bugs+=1
            self.log.warn(f"Signup failed (see DB bug above)")
            await self.snap("signup_fail")
            return False
        except Exception as e:
            self.log.error(f"Signup: {e}"); traceback.print_exc()
            return False

    async def create_prod(self):
        self.log.info("=== CREATE PRODUCT ===")
        try:
            await self.goto("/admin/products/create")
            if self.is_on("/login"):
                self.log.warn("Not admin - cannot create product")
                return False
            b=await self.page.text_content("body") or ""
            if "إضافة منتج" not in b:
                self.log.warn("Create page blocked")
                await self.snap("create_blocked")
                return False
            await self.csrf()
            await self.page.fill('input[name="title"]',PROD_TITLE)
            await self.page.select_option('select[name="category_class"]',PROD_CAT)
            await self.page.fill('input[name="price"]',PROD_PRICE)
            await self.page.fill('textarea[name="description"]',PROD_DESC)
            tmp=Path(tempfile.mktemp(suffix=".png"))
            dummy_image(str(tmp))
            await self.page.set_input_files('input[name="image"]',str(tmp))
            await self.page.click('button[type="submit"], .btn-submit')
            await self.page.wait_for_timeout(3000)
            try: tmp.unlink()
            except: pass
            b2=await self.page.text_content("body") or ""
            if PROD_TITLE in b2:
                self.log.ok(f"Product created: {PROD_TITLE}")
                await self.snap("prod_ok")
                return True
            self.log.warn(f"Product submit result: {self.url_path()}")
            await self.snap("prod_result")
            return False
        except Exception as e:
            self.log.error(f"Create product: {e}"); traceback.print_exc(); return False

    async def run(self):
        self.log.info("="*60+"\nBUG HUNTING MACHINE\n"+"="*60)
        try:
            await self.setup()
            await self.fuzz_login()
            await self.fuzz_signup()
            admin=await self.try_admin()
            if not admin:
                if await self.signup_user():
                    self.log.info("User registered. Admin role required for dashboard.")
            if admin:
                await self.create_prod()
            else:
                self.log.info("Skipping product creation - no admin session available")
                self.log.info("E2E happy path incomplete: admin password unknown + signup broken by DB bug")
        except Exception as e:
            self.log.error(f"Fatal: {e}"); traceback.print_exc()
        finally:
            await self.close()
            self.log.info("="*60+"\nMACHINE SHUTDOWN\n"+"="*60)

if __name__=="__main__":
    asyncio.run(Machine().run())
