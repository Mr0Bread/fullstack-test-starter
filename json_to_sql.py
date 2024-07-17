import json

with open('data.json') as f:
    data = json.load(f)

categories = data['data']['categories']
products = data['data']['products']

create_categories_table = """
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);
"""

create_products_table = """
CREATE TABLE IF NOT EXISTS products (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(255) NOT NULL,
    in_stock BOOLEAN NOT NULL,
    brand VARCHAR(255)
);
"""

insert_categories = "INSERT INTO categories (name) VALUES "
insert_products = "INSERT INTO products (id, name, description, price, category, in_stock, brand) VALUES "

category_values = []
for category in categories:
    name = category['name'].replace("'", "''")
    category_values.append(f"('{name}')")

product_values = []
for product in products:
    product_id = product['id'].replace("'", "''")
    name = product['name'].replace("'", "''")
    description = product.get('description', '').replace("'", "''")
    price = product['prices'][0]['amount']
    category = product['category'].replace("'", "''")
    in_stock = 1 if product['inStock'] else 0
    brand = product['brand'].replace("'", "''")
    product_values.append(f"('{product_id}', '{name}', '{description}', {price}, '{category}', {in_stock}, '{brand}')")

insert_categories += ", ".join(category_values) + ";"
insert_products += ", ".join(product_values) + ";"

with open('data.sql', 'w') as f:
    f.write(create_categories_table)
    f.write(create_products_table)
    f.write(insert_categories)
    f.write(insert_products)

print("SQL file created successfully: data.sql")
