
Mass Times View
SELECT     TOP (100) PERCENT dbo.parishes.pid, dbo.parishes.parish_name, dbo.mass_languages.language, dbo.mass_times.days, CONVERT(varchar(2),
                       DATEPART(hour, dbo.mass_times.time)) + ':' + CONVERT(varchar(2), DATEPART(minute, dbo.mass_times.time)) AS time
FROM         dbo.parishes INNER JOIN
                      dbo.mass_languages ON dbo.parishes.pid = dbo.mass_languages.pid INNER JOIN
                      dbo.mass_times ON dbo.mass_languages.mlid = dbo.mass_times.mlid
ORDER BY dbo.parishes.parish_name


Confession Times View
SELECT     dbo.parishes.pid, dbo.parishes.parish_name, dbo.confession_times.days, CONVERT(varchar(2), DATEPART(hour, dbo.confession_times.time)) 
                      + ':' + CONVERT(varchar(2), DATEPART(minute, dbo.confession_times.time)) AS time, CONVERT(varchar(2), DATEPART(hour, 
                      dbo.confession_times.endtime)) + ':' + CONVERT(varchar(2), DATEPART(minute, dbo.confession_times.endtime)) AS endtime
FROM         dbo.parishes INNER JOIN
                      dbo.confession_times ON dbo.parishes.pid = dbo.confession_times.pid

Devotion Times View
SELECT     dbo.parishes.pid, dbo.parishes.parish_name, dbo.devotion_times.dtype, dbo.devotion_times.days, CONVERT(varchar(2), DATEPART(hour, 
                      dbo.devotion_times.time)) + ':' + CONVERT(varchar(2), DATEPART(minute, dbo.devotion_times.time)) AS time
FROM         dbo.parishes INNER JOIN
                      dbo.devotion_times ON dbo.parishes.pid = dbo.devotion_times.pid

Special Mass Times View
SELECT     dbo.parishes.pid, dbo.parishes.parish_name, dbo.mass_specials.special, dbo.mass_special_times.days, CONVERT(varchar(2), 
                      DATEPART(hour, dbo.mass_special_times.time)) + ':' + CONVERT(varchar(2), DATEPART(minute, dbo.mass_special_times.time)) AS Expr1
FROM         dbo.parishes INNER JOIN
                      dbo.mass_specials ON dbo.parishes.pid = dbo.mass_specials.pid INNER JOIN
                      dbo.mass_special_times ON dbo.mass_specials.msid = dbo.mass_special_times.msid