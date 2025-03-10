
- ResourceController
  - response
    - meta
      - default_includes
      - available_includes
      - available_filters
      - available_sorts
      - available_fields
      - pagination

### Traits

- HasQueryBuilder
  - filter
  - sort
  - paginate
  - include


Tôi đang làm 1 tính năng create/update user thông tin như sau:
- User: id, name, email, avatar, password
- Create user: name, email, password, avatar (optional)
- Update user: name, email, password, avatar (optional). Avatar is optional, and if it is not provided, it should not be updated. if it is provided, it should be updated. If is_delete_avatar is provided, it should be deleted.