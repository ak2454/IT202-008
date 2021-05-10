CREATE TABLE OrderItems(
    id int auto_increment,
    product_id int,
    user_id int,
    quantity int,
    price int,
    order_id int,
    modified    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP on update current_timestamp,
    created     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    primary key (id),
    foreign key (user_id) references Users(id),
    foreign key (product_id) references Products(id),
    foreign key (order_id) references Orders(id)
)
