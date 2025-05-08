CREATE TABLE customer 
(
  customer_id NUMBER(5),
  store_id NUMBER(3),
  first_name VARCHAR2(45),
  last_name VARCHAR2(45),
  email VARCHAR2(50),
  address_id NUMBER(5),
  active CHAR(1),
  create_date TIMESTAMP(6),
  last_update TIMESTAMP(6)
  )