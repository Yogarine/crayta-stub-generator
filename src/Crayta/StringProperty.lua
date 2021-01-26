--------------------------------------------------------------------------------------------------------
--- @shape StringProperty : Property<string>
--- @alias StringPropertyOptions table<string,string>|number[]
---
--- @field type                    "string"
--- @field default                 string|nil
--- @field options  StringPropertyOptions|nil  @A list of allowed values, either named:
---                                            {Monday = “Mon”, Tuesday = “Tue”, Wednesday = “Wed”},
---                                            or unnamed: {“Monday”, “Tuesday”, “Wednesday”}
--------------------------------------------------------------------------------------------------------
local StringProperty = {}

return StringProperty
