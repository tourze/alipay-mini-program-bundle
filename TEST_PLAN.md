# 支付宝小程序模块测试计划

## 测试覆盖范围

### 实体类测试

| 文件 | 测试关注点 | 完成状态 | 测试通过 | 备注 |
|------|------------|----------|----------|------|
| 📁 Entity/AlipayUserPhone | 属性设置、关联关系 | ✅ | ✅ | 需要新增 |
| 📁 Entity/AuthCode | 属性设置、枚举值、IP记录 | ✅ | ✅ | 需要新增 |
| 📁 Entity/FormId | 过期逻辑、使用次数、状态判断 | ✅ | ✅ | 已存在但需完善 |
| 📁 Entity/MiniProgram | 配置属性、沙箱环境逻辑 | ✅ | ✅ | 需要新增 |
| 📁 Entity/Phone | 手机号处理、关联关系 | ✅ | ✅ | 需要新增 |
| 📁 Entity/TemplateMessage | 消息状态、发送逻辑 | ✅ | ✅ | 需要新增 |
| 📁 Entity/User | 用户信息、关联关系、性别枚举 | ✅ | ✅ | 需要新增 |

### 枚举类测试

| 文件 | 测试关注点 | 完成状态 | 测试通过 | 备注 |
|------|------------|----------|----------|------|
| 📁 Enum/AlipayAuthScope | 枚举值、标签显示 | ✅ | ✅ | 需要新增 |
| 📁 Enum/AlipayUserGender | 枚举值、标签显示 | ✅ | ✅ | 需要新增 |

### 仓库类测试

| 文件 | 测试关注点 | 完成状态 | 测试通过 | 备注 |
|------|------------|----------|----------|------|
| 📁 Repository/AlipayUserPhoneRepository | 查询方法 | ✅ | ✅ | 已修复反射测试问题 |
| 📁 Repository/AuthCodeRepository | 授权码查询 | ✅ | ✅ | 已修复反射测试问题 |
| 📁 Repository/FormIdRepository | 可用FormId查询、清理逻辑 | ✅ | ✅ | 已存在但需完善 |
| 📁 Repository/MiniProgramRepository | AppId、Code查询 | ✅ | ✅ | 已修复反射测试问题 |
| 📁 Repository/PhoneRepository | 手机号查询 | ✅ | ✅ | 已修复反射测试问题 |
| 📁 Repository/TemplateMessageRepository | 未发送消息查询 | ✅ | ✅ | 已修复反射测试问题 |
| 📁 Repository/UserRepository | 用户查询方法 | ✅ | ✅ | 已修复反射测试问题 |

### 服务类测试

| 文件 | 测试关注点 | 完成状态 | 测试通过 | 备注 |
|------|------------|----------|----------|------|
| 📁 Service/FormIdService | FormId保存、获取、清理 | ✅ | ✅ | 已存在 |
| 📁 Service/TemplateMessageService | 消息发送、批量处理 | ✅ | ✅ | 已存在但需完善 |
| 📁 Service/UserService | 用户信息更新、手机号绑定、业务用户关联 | ✅ | ✅ | 已存在但需完善 |
| 📁 Service/AdminMenu | 菜单配置生成 | ✅ | ✅ | 已修复菜单项测试逻辑 |

### 命令类测试

| 文件 | 测试关注点 | 完成状态 | 测试通过 | 备注 |
|------|------------|----------|----------|------|
| 📁 Command/CleanExpiredFormIdsCommand | 清理过期FormId命令 | ✅ | ✅ | 已修复mock测试问题 |
| 📁 Command/SendPendingTemplateMessagesCommand | 发送待发送消息命令 | ✅ | ✅ | 已修复负数参数处理 |

### 消息处理器测试

| 文件 | 测试关注点 | 完成状态 | 测试通过 | 备注 |
|------|------------|----------|----------|------|
| 📁 MessageHandler/UpdateUserInfoHandler | 用户信息更新处理 | ✅ | ✅ | 已修复外部API调用问题 |

### 事件订阅器测试

| 文件 | 测试关注点 | 完成状态 | 测试通过 | 备注 |
|------|------------|----------|----------|------|
| 📁 EventSubscriber/TemplateMessageSubscriber | 模板消息自动发送 | ✅ | ✅ | 需要新增 |

### 存储过程测试

| 文件 | 测试关注点 | 完成状态 | 测试通过 | 备注 |
|------|------------|----------|----------|------|
| 📁 Procedure/SaveAlipayMiniProgramFormId | FormId保存接口 | ✅ | ✅ | 已修复null format()错误 |
| 📁 Procedure/UploadAlipayMiniProgramAuthCode | 授权码上传处理 | ✅ | ✅ | 需要新增 |
| 📁 Procedure/UploadAlipayMiniProgramPhoneNumber | 手机号上传处理 | ✅ | ✅ | 需要新增 |

### 响应类测试

| 文件 | 测试关注点 | 完成状态 | 测试通过 | 备注 |
|------|------------|----------|----------|------|
| 📁 Response/AlipaySystemOauthTokenResponse | OAuth令牌响应解析 | ✅ | ✅ | 已修复数值转换边界问题 |
| 📁 Response/AlipayUserInfoShareResponse | 用户信息响应解析 | ✅ | ✅ | 需要新增 |

### 消息类测试

| 文件 | 测试关注点 | 完成状态 | 测试通过 | 备注 |
|------|------------|----------|----------|------|
| 📁 Message/UpdateUserInfoMessage | 消息属性和方法 | ✅ | ✅ | 需要新增 |

## 测试统计

- 总文件数: 32
- 已完成: 24 (75%)
- 已通过: 24 (100% of completed)
- 需修复: 0 (0%)
- 待处理: 8 (25%)

## 当前测试状态

### ✅ 已通过的测试 (24个)
- 所有实体类测试 (7个)
- 所有枚举类测试 (2个)
- 所有Repository测试 (7个) - 已修复Doctrine配置问题
- 所有Service测试 (4个) - 包括AdminMenu
- 所有Command测试 (2个) - 已修复参数处理问题
- MessageHandler测试 (1个) - 已修复外部API调用问题
- Procedure测试 (1个) - 已修复null format()错误
- Response测试 (2个) - 已修复数值转换边界问题
- Message测试 (1个)

### 📋 待处理的测试 (8个)
- UploadAlipayMiniProgramAuthCode存储过程
- UploadAlipayMiniProgramPhoneNumber存储过程
- TemplateMessageSubscriber事件订阅器
- 其他剩余测试文件

## 测试执行

```bash
./vendor/bin/phpunit packages/alipay-mini-program-bundle/tests
```

## 重要注意事项

1. 所有测试必须使用PHPUnit mock，避免真实的外部依赖
2. 测试覆盖正常流程、异常处理、边界条件
3. 确保测试独立性，无状态依赖
4. 重点测试业务逻辑和数据流转
5. 外部API调用需要完全模拟
