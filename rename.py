import os, re

path = r'd:\laragon\www\SimonikPKTJ'

# Pengecualian direktori agar tidak mengubah file sistem/vendor
exclude_dirs = {'.git', 'vendor', 'node_modules', 'writable', 'system', '.agents'}

for root, dirs, files in os.walk(path):
    dirs[:] = [d for d in dirs if d not in exclude_dirs]
    for file in files:
        if file.endswith(('.php', '.md')):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
                
            if 'bawahan' in content.lower():
                content = re.sub(r'bawahan', 'staf', content)
                content = re.sub(r'Bawahan', 'Staf', content)
                content = re.sub(r'BAWAHAN', 'STAF', content)
                
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(content)
                print(f'Updated {filepath}')
