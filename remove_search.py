import os

files = [
    "products.php",
    "sginup.php",
    "signin.php",
    "services.php",
    "contact_us.php",
    "about_us.php"
]

search_block = """          <div class="search_container">
            <input type="text" id="search-input" placeholder="ابحث عن الأجهزة...">
            <i class="fa-solid fa-magnifying-glass search_icon"></i>
          </div>"""

for file in files:
    path = os.path.join(r"c:\xampp\htdocs\e-co", file)
    if os.path.exists(path):
        with open(path, "r", encoding="utf-8-sig") as f:
            content = f.read()
        
        # Replace the literal block
        modified = content.replace(search_block, "")
        
        if content != modified:
            with open(path, "w", encoding="utf-8-sig") as f:
                f.write(modified)
            print(f"Updated {file}")
        else:
            print(f"Could not find exact block in {file}, falling back to substring search.")
            # Fallback block removal to be more resilient to whitespace changes
            lines = content.splitlines(True)
            out_lines = []
            skip = False
            for line in lines:
                if '<div class="search_container">' in line:
                    skip = True
                elif skip and '</div>' in line:
                    skip = False
                    continue
                
                if not skip:
                    out_lines.append(line)
            
            with open(path, "w", encoding="utf-8-sig") as f:
                f.writelines(out_lines)
